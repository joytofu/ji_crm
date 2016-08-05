<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/12/16
 * Time: 3:06 PM
 */

namespace CRMBundle\Controller;


use AppBundle\Controller\CRMPaginator;
use AppBundle\Controller\GlobalClass;
use CRMBundle\Entity\Distribution;
use CRMBundle\Entity\Mapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

/**
 * @Route("/distribution")
 */
class DistributionController extends Controller
{

    /**
     * @Route("/distributed_users/{sort}/{id}",name="distributed_users")
     * @Security("has_role('RETRIEVE_DISTRIBUTION')")
     */
    public function distributedUsers(Request $request,$sort = null,$id = null){

        $distributed = 'distributed';
        $em = $this->getDoctrine()->getManager();

        if($sort==null){
            //get clients data from 51jili
            $output = $em->getRepository('CRMBundle:Distribution')->findUidsByManager(null,$request);
            $v = $output['string'];
            $apiRes = GlobalClass::apiGetData('user',$k = 'uids',$v);
            $userInfo = $apiRes['Data']['list'];
            $pagination = $output['pagination'];
        }elseif($sort=='category' && $id!=null){
            //get clients data from 51jili
            $output = $em->getRepository('CRMBundle:ClientCategoryMapping')->getUidsStringByCategory($id,$request);
            $v = $output['string'];
            $apiRes = GlobalClass::apiGetData('user',$k = 'uids',$v);
            $userInfo = $apiRes['Data']['list'];
            $pagination = $output['pagination'];
        }elseif($sort=='manager' && $id!=null){
            //get clients data from 51jili
            $output = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($id,$request);
            $v = $output['string'];
            $apiRes = GlobalClass::apiGetData('user',$k = 'uids',$v);
            $userInfo = $apiRes['Data']['list'];
            $pagination = $output['pagination'];
        }elseif($sort=='verifications' && $id!=null){
            switch ($id){
                case "ID":
                    $v = array('verifications'=>array('ID'=>'255'));
                    break;
                case "Phone":
                    $v = array('verifications'=>array('Phone'=>'1'));
                    break;
                case "Email":
                    $v = array('verifications'=>array('Email'=>'1'));
                    break;
            }
            //$v = array("verifications"=>array("ID"=>$id=='ID'?true:null,"cellphone"=>$id=='cellphone'?true:null,"email"=>$id=='email'?true:null));
            $v = json_encode($v);
            if($page = $request->get('page')){
                $apiRes = GlobalClass::apiGetData('uids',$k='search',$v,$page);
            }else{
                $apiRes = GlobalClass::apiGetData('uids',$k='search',$v);
            }
            //$apiRes = GlobalClass::apiGetData('uids',$k='search',$v);
            $uids = $apiRes['Data'];  //retrieve uids first

            $page_count = $apiRes['Page']['all'];
            $paginator = new CRMPaginator($request,null);
            $pagination = $paginator->paginate($page_count);

            $callback = function ($e){
                return array('uid'=>$e);
            };
            $uidsArray = array_map($callback,$uids);
            $uidsString = GlobalClass::arrayToString($uidsArray);
            $apiRes = GlobalClass::apiGetData('user',$k='uids',$uidsString);  //get user info data with uids retrieved above
            $userInfo = $apiRes['Data']['list'];

        }elseif($sort=='asset' && $id!=null){
            switch($id){
                case '0':
                    $v = "0,0";
                    break;
                case '0.01-1':
                    $v = "0.01,1";
                    break;
                case '1-100':
                    $v = "1,100";
                    break;
                case '100-1000':
                    $v = "100,1000";
                    break;
                case 'over1M':
                    $v = "1000,1001";
                    break;
            }

            if($page = $request->get('page')){
                $apiRes = GlobalClass::apiGetData('user',$k='asset',$v,$page);
            }else{
                $apiRes = GlobalClass::apiGetData('user',$k='asset',$v);
            }
            $page_count = $apiRes['Data']['page']['all'];
            $paginator = new CRMPaginator($request,null);
            $pagination = $paginator->paginate($page_count);

            $userInfo = $apiRes['Data']['list'];

            $uidsArray = array_column($apiRes,'uid');
            $callback = function ($e){
                return array('uid'=>$e);
            };
            $uidsArray = array_map($callback,$uidsArray);


        }elseif($sort=='registration' && $id!=null){

        }elseif($sort=='channel'){

        }
        
        if($sort!='verifications' && $sort!='asset'){
            $uidsArray = $output['array'];
        }

        $res = GlobalClass::getManagersAndCategoriesByUidsArray($uidsArray,$em);
        $managers = $res['managers'];
        $categories = $res['categories'];

        //merge arrays
        $data = GlobalClass::dataMerge($categories, $managers);
        $data = GlobalClass::dataMerge($data, $uidsArray);
        $data = GlobalClass::dataMerge($data,$userInfo);
        
        //sorting
        $sorting = $this->getDistributionSorting();

        $managersToDistributeTo = $em->getRepository('UserBundle:UserInfo')->findBy(['department'=>'客服部']);
        
        return $this->render('@CRM/Distribution/distributed_users.html.twig',[
            'data'=>$data,
            'pagination'=>$pagination,
            'distributed'=>$distributed,
            'sorting'=>$sorting,
            'sort'=>$sort,
            'id'=>$id,
            'managersToDistributeTo'=>$managersToDistributeTo
            
        ]);
    }

    /**
     * @Route("/multiple_sort",name="multiple_sort")
     * @Security("has_role('RETRIEVE_DISTRIBUTION')")
     */
    public function distributedUsersByMultipleSort(Request $request){

        $em = $this->getDoctrine()->getManager();
        $midsArr = $request->get('manager');
        $categoryIdsArr = $request->get('category');

        //conditions to get data from 51jili
        $v = array();
        $verificationsCondition = $request->get('verifications');
        if(is_array($verificationsCondition)){
            if(in_array('ID',$verificationsCondition)){
                $verifications['ID']='255';
            }
            if(in_array('Phone',$verificationsCondition)){
                $verifications['Phone']='1';
            }
            if(in_array('Email',$verificationsCondition)){
                $verifications['Email']='1';
            }
            $v['verifications'] = $verifications;
        }

        $assetCondition = $request->get('asset');
        if(is_array($assetCondition)){
            if(in_array('0',$assetCondition)){
                $asset[]='BETWEEN 0 AND 0 ';
            }
            if(in_array('0.01-1',$assetCondition)){
                $asset[]='BETWEEN 0.01 AND 1000 ';
            }
            if(in_array('1-100',$assetCondition)){
                $asset[]='BETWEEN 1000 AND 100000 ';
            }
            if(in_array('100-1000',$assetCondition)){
                $asset[]='BETWEEN 100000 AND 1000000 ';
            }
            if(in_array('over1M',$assetCondition)){
                $asset[]='>=1000000';
            }
            $v['asset'] = $asset;
        }
        $conditions = array(
            'manager'=>$midsArr,
            'category'=>$categoryIdsArr,
            'verifications'=>$verificationsCondition,
            'asset'=>$assetCondition
        );


        $registrationCondition = $request->get('registration');

        $output = $em->getRepository('CRMBundle:Distribution')->findUidsByManagerAndCategory($midsArr,$categoryIdsArr);
        $uids_string = GlobalClass::arrayToString($output); //filter uids with mids and cate_ids first
        $v['extraUids'] = $uids_string;
        $session = $request->getSession();
        //request api with multiple conditions and extra uids
        if($page = $request->get('page')){
            $v = $session->get('v');
            $conditions = $session->get('conditions');
            $apiRes = GlobalClass::apiGetData('uids',$k='search',$v,$page);
        }else{
            $v = json_encode($v);
            $session->set('v',$v);
            $session->set('conditions',$conditions);
            $apiRes = GlobalClass::apiGetData('uids',$k='search',$v);
        }
        $page_count = $apiRes['Page']['all'] ? $apiRes['Page']['all'] :0;
        $paginator = new CRMPaginator($request,null);
        $pagination = $paginator->paginate($page_count);

        //array('2','4','6','7')-------->array(['uid'=>'2'],['uid'=>'4'],['uid'=>'6'],['uid'=>'7'])
        $uidsArray = $apiRes['Data'];
        $newUidsString = implode(",",$uidsArray);
        $callback = function ($e){
            return array('uid'=>$e);
        };
        $uidsArray = array_map($callback,$uidsArray);  //return uid array

        $res = GlobalClass::getManagersAndCategoriesByUidsArray($uidsArray,$em);
        $managers = $res['managers'];
        $categories = $res['categories'];

        //$apiRes = GlobalClass::apiGetData('user','uids',$newUidsString);
        if($request->get('uids')){
            $uids_string = $request->get('uids');
        }else{
            $uids_string = $newUidsString;
        }
        $url = GlobalClass::API."user?k=uids&v=".$uids_string;
        $apiRes = GlobalClass::curlGet($url);
        $apiRes = json_decode($apiRes,true);
        $userInfo = $apiRes['Data']['list'];

        //merge arrays
        $data = GlobalClass::dataMerge($categories, $managers);
        $data = GlobalClass::dataMerge($data, $uidsArray);
        $data = GlobalClass::dataMerge($data,$userInfo);

        //sorting
        $sorting = $this->getDistributionSorting();

        $distributed = 'distributed';

        $managersToDistributeTo = $em->getRepository('UserBundle:UserInfo')->findBy(['department'=>'客服部']);

        return $this->render('@CRM/Distribution/distributed_users.html.twig',[
            'conditions'=>$conditions,
            'data'=>$data,
            'pagination'=>$pagination,
            'distributed'=>$distributed,
            'sorting'=>$sorting,
            'uids_string'=>$newUidsString,
            'managersToDistributeTo'=>$managersToDistributeTo

        ]);


    }

    /**
     * @Route("/undistributed_users",name="undistributed_users")
     * @Security("has_role('RETRIEVE_DISTRIBUTION')")
     */
    public function undistributedUsers(){
        $em = $this->getDoctrine()->getManager();

        //get data from 51jili
        $distributedUsersIds = $em->getRepository('CRMBundle:Distribution')->findAllDistributedUids();

        
    }

    /**
     * @Route("/distribute",name="distribute")
     * @Security("has_role('MODIFY_DISTRIBUTION')")
     */
    public function distribute(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uids = $request->get('uids');
        $mid = $request->get('mid');
        $em->getRepository('CRMBundle:Distribution')->updateManager($mid,$uids);
        $url = $this->generateUrl('distributed_users');
        return new Response("<script>alert('客户分配成功!');window.location.href='$url'</script>");
    }

    /**
     * @Route("/group",name="distribution_group")
     * @Security("has_role('RETRIEVE_DISTRIBUTION')")
     */
    public function distributionGroup(Request $request){
        $em = $this->getDoctrine()->getManager();

        $managers = $em->getRepository('UserBundle:UserInfo')->getAllManagers();

        //pass $uids_arr to retrieve asset data from 51jili
        $uidsArray = array();
        $callback = function($manager) use(&$uidsArray,$em,$request){
            $output = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($manager['id'],$request);
            $uidsString = $output['string'];
            if(null!=$uidsString){
                $uidsArray[$manager['id']] = $uidsString;
            }
        };
        array_walk($managers,$callback);
        $v = json_encode($uidsArray);

        $apiRes = GlobalClass::apiGetData('group','asset',$v);

        $group = array();
        foreach($managers as $manager){
            $output = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($manager['id'],$request);
            if(!empty($output['array'])){
                $mid = $manager['id'];
                $group[$mid]['mid'] = $mid;
                $group[$mid]['name'] = $manager['name'];
                $group[$mid]['clientsCount'] = $em->getRepository('CRMBundle:Distribution')->countClientsOfManager($mid);
            }
        }
        $assetData = $apiRes['Data'];

        $group = GlobalClass::dataMerge($group,$assetData);
        
        return $this->render('@CRM/Distribution/group.html.twig',['group'=>$group]);
    }



    protected function getDistributionSorting($ifDistributed = true){
        if($ifDistributed==true){
            $em = $this->getDoctrine()->getManager();
            $managers = $em->getRepository('UserBundle:UserInfo')->getDistinctManagers();
            $sorted_managers = array();
            foreach($managers as $manager){
                $sorted_managers[$manager['name']] = $manager['id'];
            }
            return array(
                '客户类型'=>['category'=>['再致电用户'=>'1','一般客户'=>'2','意向客户'=>'3','无意向客户'=>'4','VIP客户'=>'5','集成员工'=>'6','员工家属'=>'7','员工推荐'=>'8','投资客户推荐'=>'9']],
                '身份认证'=>['verifications'=>['身份证认证'=>'ID','手机认证'=>'Phone','邮箱认证'=>'Email']],
                '资产级别'=>['asset'=>['0'=>'0','0.01-1K'=>'0.01-1','1k-100K'=>'1-100','100k-1M'=>'100-1000','1M以上'=>'over1M']],
                '注册时间'=>['registration'=>['2014-2015'=>'1','2015-2016'=>'2']],
                '接入渠道'=>['channel'=>['网站'=>'1','微信'=>'2']],
                '客服人员'=>['manager'=>$sorted_managers]
            ); 
        }else{
            return array(
                '身份认证'=>['verified'=>['已经验证'=>1,'待验证'=>0]],
                '注册时间'=>['registration'=>[]]
            );
        }
        
    }

    
    
    
}