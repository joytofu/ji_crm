<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/8/16
 * Time: 3:43 PM
 */

namespace CRMBundle\Controller;


use AppBundle\Controller\CRMPaginator;
use AppBundle\Controller\GlobalClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAssetController extends Controller
{

    /**
     * @param $uid
     * @param $type <p>all(transaction),tp(Tender),gc(GetCash),rc(Recharge),rp(BackPrincipal),lg(log record)</p>
     * @param $bid
     * @return Response
     */
    public function recordAction($uid,$type,$bid){
        $apiRes = GlobalClass::apiGetData('record',$type,$uid);
        if(isset($apiRes['Data']['page'])){
            $totalPages = $apiRes['Data']['page']['pages'];
            return $this->render('CRMBundle:ClientManage:ClientAsset.html.twig',[
                'uid'=>$uid,
                'bid'=>$bid,
                'totalPages'=>$totalPages,
                'type'=>$type]);

        }else{
            return $this->render('CRMBundle:ClientManage:ClientAsset.html.twig',['bid'=>$bid]);
        }
    }

    /**
     * @Route("/get_record",name="get_record")
     */
    public function getRecordWithAjax(Request $request){
        $type = $request->get('type') ? $request->get('type') : null;
        $uid = $request->get('uid') ? $request->get('uid') : null;
        $page = $request->get('record_page') ? $request->get('record_page') : null;
        $apiRes = GlobalClass::apiGetData('record',$type,$uid,$page);
        $data = $apiRes['Data']['list'];
        return new JsonResponse($data);
    }


    /**
     * <p>$type:rc/gc</p>
     * @Route("/fund_flow/{type}/{export}",name="fund_flow_action")
     */
    public function fundFlowAction($type=null,$bid=null,$export=null,Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($user->hasRole('ROLE_SALESMAN') || $user->hasRole('ROLE_DEPARTMENT_MANAGER')){
            $mid = $user->getUserInfo()->getId();
            $data = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($mid,$request,true);
        }else{
            $data = $em->getRepository('CRMBundle:Distribution')->findUidsByManager(null,$request,true);
        }

        //get total pages from 51jili
        $uidsString = $data['string'];
        if($export){
            $apiRes = GlobalClass::apiGetData('grouprecord',$type,$uidsString,null,null,null,false);
            if(isset($apiRes['Data']['list'])){
                $data = $apiRes['Data']['list'];
                $this->forward('AppBundle:Export:export',['data'=>$data]);
            }
        }
        $apiRes = GlobalClass::apiGetData('grouprecord',$type,$uidsString);
        if(isset($apiRes['Data']['page'])){
            $totalPages = $apiRes['Data']['page']['pages'];
            return $this->render('CRMBundle:ClientManage:FundFlow.html.twig',[
                'type'=>$type,
                'totalPages' => $totalPages,
                'bid'=>$bid
            ]);
        }else{
            return $this->render('CRMBundle:ClientManage:FundFlow.html.twig',['bid'=>$bid]);
        }
    }

    /**
     * @Route("/get_fund_flow",name="get_fund_flow")
     */
    public function getFundFlowWithAjax(Request $request){
        $type = $request->get('type') ? $request->get('type') : null;
        $page = $request->get('record_page') ? $request->get('record_page') : null;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($user->hasRole('ROLE_SALESMAN') || $user->hasRole('ROLE_DEPARTMENT_MANAGER')){
            $mid = $user->getUserInfo()->getId();
            $data = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($mid,$request,true);
        }else{
            $data = $em->getRepository('CRMBundle:Distribution')->findUidsByManager(null,$request,true);
        }

        //get data from 51jili
        $uidsString = $data['string'];
        $apiRes = GlobalClass::apiGetData('grouprecord',$type,$uidsString,$page);
        $data = $apiRes['Data']['list'];
        return new JsonResponse($data);
    }



    /**
     * @Route("/investment/{mid}",name="investment_by_manager")
     */
    public function investmentRecordByManager($mid,Request $request){
        $em = $this->getDoctrine()->getManager();
        //$manager = $em->getRepository('UserBundle:User')->find($mid);

        //get data from 51jili
        $result = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($mid,$request);
        $uids = $result['string'];
        $pagination = $result['pagination'];

        $record = Array
        (
            '0' => Array
            (
                'serialNumber' => 'A0001',
                'type' => '马上盈',
                'deposit' => '1000000',
                'expense' => '56788',
                'balance' => '899912',
                'createdAt' => '2016-03-14 10:34:12'
            ),
            '1' => Array
            (
                'serialNumber' => 'B0002',
                'type' => '投资收益',
                'deposit' => '20000',
                'expense' => '2000',
                'balance' => '99999',
                'createdAt' => '2016-05-02 14:11:42'
            ),
        );
        $page_header = '客户投资列表';
        
        return $this->render('CRMBundle:ClientManage:ClientAssetByManager.html.twig',['record'=>$record,'page_header'=>$page_header]);
    }



    /**
     * @Route("/payback/{mid}",name="payback_by_manager")
     */
    public function paybackRecordByManager($mid,Request $request){
        $em = $this->getDoctrine()->getManager();
        //$manager = $em->getRepository('UserBundle:User')->find($mid);

        //get data from 51jili
        $data = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($mid,$request);
        $uids = $data['string'];
        $uids = array('uids'=>$uids);


        $pagination = $data['pagination'];

        $record = Array
        (
            '0' => Array
            (
                'serialNumber' => 'P000001',
                'type' => '回款',
                'deposit' => '3000',
                'expense' => '',
                'balance' => '100000',
                'createdAt' => '2016-03-17 11:11:12'
            ),
            '1' => Array
            (
                'serialNumber' => 'P000002',
                'type' => '回款',
                'deposit' => '4000',
                'expense' => '',
                'balance' => '1400000',
                'createdAt' => '2016-04-17 17:11:42'
            ),
        );
        $page_header = '客户回款列表';
        return $this->render('@CRM/ClientManage/ClientAssetByManager.html.twig',['record'=>$record,'page_header'=>$page_header,'pagination'=>$pagination]);
    }


    /**
     * @Route("/payback_plan/{sorting}/{export}",name="payback_plan")
     * @Security("has_role('RETRIEVE_PAYBACK_PLAN')")
     */
    public function paybackPlan(Request $request,$sorting=null,$export=null){

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CRMBundle:Distribution');
        if($this->isGranted('ROLE_SENIOR_EXECUTIVE')){
            $output = $repository->findUidsByManager(null,$request,true);
        }else{
            $manager = $this->getUser();
            $mid = $manager->getUserInfo()->getId();
            $output = $repository->findUidsByManager($mid,$request,true);
        }

        //get data from 51jili
        $uidsString = $output['string'];

        //export to Excel
        if($export){
            $apiRes = GlobalClass::apiGetData('grouprecord','rp',$uidsString,null,null,$sorting,false);
            if(isset($apiRes['Data']['list'])){
                $data = $apiRes['Data']['list'];
                $this->forward('AppBundle:Export:export',['data'=>$data]);
            }
        }

        if($page = $request->get('page')){
            $apiRes = GlobalClass::apiGetData('grouprecord','rp',$uidsString,$page,null,$sorting);
        }else{
            $apiRes = GlobalClass::apiGetData('grouprecord','rp',$uidsString,null,null,$sorting);
        }
        if(isset($apiRes['Data']['list'])&&isset($apiRes['Data']['page'])){
            $data = $apiRes['Data']['list'];
            $count = $apiRes['Data']['page']['all'];
            $paginator = new CRMPaginator($request,null);
            $pagination = $paginator->paginate($count);
            return $this->render('@CRM/ClientManage/paybackPlan.html.twig',[
                'data'=>$data,
                'sorting'=>$sorting,
                'pagination'=>$pagination]);
        }else{
            return $this->render('@CRM/ClientManage/paybackPlan.html.twig',[]);
        }

        /*$callback = function($item) use($sorting){
            if($sorting=='week'){
                if($item['paybackAt']<=time()+604800){
                    return $item;
                }
            }elseif($sorting=='month'){
                if($item['paybackAt']<=time()+2592000){
                    return $item;
                }
            }elseif($sorting=='3months'){
                if($item['paybackAt']<=time()+7776000){
                    return $item;
                }
            }
        };
        if($sorting!=null){
            $data = array_filter($data,$callback);
        }*/
    }
}