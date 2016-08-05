<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/6/16
 * Time: 4:33 PM
 */

namespace CRMBundle\Controller;

use AppBundle\Controller\CRMPaginator;
use AppBundle\Controller\ExportToExcel;
use AppBundle\Controller\GlobalClass;
use CRMBundle\Entity\TrackingRecord;
use CRMBundle\Form\TrackingRecordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CRMBundle\Entity\Distribution;
use UserBundle\Entity\User;

class ClientManagerController extends Controller
{

    /**
     * @Route("/myclients",name="my_clients")
     */
    public function myClients(Request $request){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CRMBundle:Distribution');
        $manager = $this->getUser();

        //get data from 51jili
        if($this->isGranted('ROLE_SENIOR_EXECUTIVE')){
            $output = $repository->findUidsByManager(null,$request);
        }else{
            $mid = $manager->getUserInfo()->getId();
            $output = $repository->findUidsByManager($mid,$request);
        }
        $uidsString = $output['string'];
        $apiRes = GlobalClass::apiGetData('user','uids',$uidsString,null,$t="detail");
        $clientInfo = $apiRes['Data']['list'];

        $pagination = $output['pagination'];

        $uidsArray = $output['array'];
        $res = GlobalClass::getManagersAndCategoriesByUidsArray($uidsArray,$em);
        $managers = $res['managers'];
        $categories = $res['categories'];
        $clientsData = GlobalClass::dataMerge($managers,$categories);
        $clientsData = GlobalClass::dataMerge($clientsData,$uidsArray);
        if(isset($apiRes['Data']['list'])){
            $clientInfo =  $apiRes['Data']['list'];
            $clientsData = GlobalClass::dataMerge($clientsData,$clientInfo);
        }
        
        return $this->render('@CRM/ClientManage/myClients.html.twig',[
            'clients'=>$clientsData,
            'pagination'=>$pagination]);
    }

    /**
     * @Route("/clients/{mid}/{export}",name="clients_of_manager")
     */
    public function getClientsByManager($mid,Request $request,$id='yes',$export=null){
        if($mid==null){
            return $this->redirectToRoute('my_clients');
        }

        $em = $this->getDoctrine()->getManager();
        $managerInfo = $em->getRepository('UserBundle:UserInfo')->find($mid);
        $managerName = $managerInfo->getName();
        $repository = $em->getRepository('CRMBundle:Distribution');

        if(!$export){
            //get data from 51jili
            $output = $repository->findUidsByManager($mid,$request);
            $uidsString = $output['string'];
            $apiRes = GlobalClass::apiGetData('user','uids',$uidsString,null,'detail');

        }else{
            $output = $repository->findUidsByManager($mid,$request,true);
            $uidsString = $output['string'];
            $apiRes = GlobalClass::apiGetData('user','uids',$uidsString,null,'detail',null,false);
        }

        //merge data
        $uidsArray = $output['array'];
        $res = GlobalClass::getManagersAndCategoriesByUidsArray($uidsArray,$em);
        $managers = $res['managers'];
        $categories = $res['categories'];
        $clientsData = GlobalClass::dataMerge($managers,$categories);
        $clientsData = GlobalClass::dataMerge($clientsData,$uidsArray);
        if(isset($apiRes['Data']['list'])){
            $clientInfo =  $apiRes['Data']['list'];
            $clientsData = GlobalClass::dataMerge($clientsData,$clientInfo);
        }

        if($export){
            $this->forward('AppBundle:Export:export',['data'=>$clientsData]);
        }
        $pagination = $output['pagination'];


        return $this->render('@CRM/ClientManage/myClients.html.twig',[
            'clients'=>$clientsData,
            'pagination'=>$pagination,
            'id'=>$id,
            'mid'=>$mid,
            'manager_name'=>$managerName]);

    }

    /**
     * @Route("/export_my_clients",name="export_my_clients")
     */
    public function exportMyClients(Request $request){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CRMBundle:Distribution');

        //get data from 51jili
        if($this->isGranted('ROLE_SENIOR_EXECUTIVE')){
            $output = $repository->findUidsByManager(null,$request,true);
        }else{
            $manager = $this->getUser();
            $mid = $manager->getUserInfo()->getId();
            $output = $repository->findUidsByManager($mid,$request,true);
        }
        $uidsString = $output['string'];
        $apiRes = GlobalClass::apiGetData('user','uids',$uidsString,null,$t="detail",null,false);
        $clientInfo = $apiRes['Data']['list'];
        $this->forward('AppBundle:Export:export',['data'=>$clientInfo]);
    }




    /**
     * @Route("/client_detail/{uid}",name="client_detail")
     */
    public function clientDetail($uid,Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($user->hasRole('ROLE_SALESMAN')){
            $mid = $em->getRepository('CRMBundle:Distribution')->findOneBy(['uid'=>$uid])->getMid();
            if($user->getUserInfo()->getId()!=$mid){
                $url = $this->generateUrl('my_clients');
                return new Response("<script>alert('您无法查看非分配客户的信息!');window.location.href='$url';</script>");
            }
        }
        //get data from 51jili
        $apiRes = GlobalClass::apiGetData('user','uids',$uid,null,'detail');
        $clientInfo = $apiRes['Data']['list'][0];
        return $this->render('@CRM/ClientManage/clientDetail.html.twig',[
            'uid'=>$uid,
            'info'=>$clientInfo
        ]);
       /* $transactionRes = GlobalClass::apiGetData('record','all',$uid);
        if(isset($transactionRes['Data']['page'])){
            $count = $transactionRes['Data']['page']['all'];
            $paginator = new CRMPaginator($request,null);
            $pagination = $paginator->paginate($count);
            $transactionInfo = $transactionRes['Data']['list'];
            return $this->render('@CRM/ClientManage/clientDetail.html.twig',[
                'uid'=>$uid,
                'info'=>$clientInfo,
                'transactionInfo'=>$transactionInfo,
                'pagination'=>$pagination
            ]);
        }else{
            $transactionInfo = null;
            return $this->render('@CRM/ClientManage/clientDetail.html.twig',[
                'uid'=>$uid,
                'info'=>$clientInfo,
                'transactionInfo'=>$transactionInfo,
            ]);
        }*/
    }

    /**
     * @Route("/tracking_record/{uid}",name="tracking_record")
     */
    public function trackingRecordsOfSingleClientAction($uid,Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($this->isGranted('ROLE_DEPARTMENT_MANAGER')){
            $trackingRecords = $em->getRepository('CRMBundle:TrackingRecord')->findBy(['uid'=>$uid]);
        }elseif($user->hasRole('ROLE_SALESMAN')){
            $mid = $user->getUserInfo()->getId();
            if($distribution = $em->getRepository('CRMBundle:Distribution')->findOneBy(['uid'=>$uid,'mid'=>$mid])){
                $trackingRecords = $em->getRepository('CRMBundle:TrackingRecord')->findBy(['uid'=>$uid,'mid'=>$mid]);
            }else{
                $url = $this->generateUrl('my_clients');
                return new Response("<script>alert('该客户归属其他客服人员,您无法查看相关回访记录!');window.location.href='$url';</script>");
            }
        }
        $trackingRecord = new TrackingRecord();
        $form = $this->createForm(TrackingRecordType::class,$trackingRecord);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $userInfo = $user->getUserInfo();
            $trackingRecord->setUid($uid)->setMid($userInfo->getId())->setHandler($userInfo);
            $em->persist($trackingRecord);
            $em->flush();
            $url = $this->generateUrl('client_detail',['uid'=>$uid]);
            return new Response("<script>alert('添加回访记录成功!');window.location.href='$url';</script>");
        }


        return $this->render('@CRM/ClientManage/trackingRecord.html.twig',[
            'trackingRecords'=>$trackingRecords,
            'uid'=>$uid,
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/edit_tracking_record",name="edit_tracking_record")
     */
    public function editTrackingRecordAction(Request $request){
        if(!$this->getUser()->hasRole('MODIFY_TRACKING_RECORD')){
            $url = $this->generateUrl('my_clients');
            return new Response("<script>alert('您没有权限修改回访记录');window.location.href='$url'</script>");
        }
        $em = $this->getDoctrine()->getManager();
        $record_id = $request->get('editRecordId');
        $contactMethod = $request->get('editContactMethod');
        $type = $request->get('editType');
        $content = $request->get('editContent');
        $editTrackingRecord = $em->getRepository('CRMBundle:TrackingRecord')->find($record_id);
        $editTrackingRecord->setContactMethod($contactMethod);
        $editTrackingRecord->setType($type);
        $editTrackingRecord->setContent($content);
        $em->flush();
        $data = array(
            'contactMethod'=>$editTrackingRecord->getContactMethod(),
            'recordType'=>$editTrackingRecord->getType(),
            'recordContent'=>$editTrackingRecord->getContent()
        );
        return new Response(json_encode($data));
    }

    /**
     * @Route("/delete_tracking_record/{id}",name="delete_tracking_record")
     */
    public function deleteTrackingRecord(TrackingRecord $record){
        if(!$this->getUser()->hasRole('DELETE_TRACKING_RECORD')){
            $url = $this->generateUrl('my_clients');
            return new Response("<script>alert('您没有权限删除回访记录');window.location.href='$url'</script>");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($record);
        $em->flush();
        return new Response('lol!!');
    }

    /**
     * @Route("/tracking_records/statistics",name="tracking_records_statistics")
     * @Security("has_role('RETRIEVE_TRACKING_RECORDS_STATISTICS')")
     */
    public function trackingRecordsStatistics(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('CRMBundle:TrackingRecord');
        $user = $this->getUser();
        if($this->isGranted('ROLE_SENIOR_EXECUTIVE')){
            $mid = null;
        }else{
            $mid = $user->getUserInfo()->getId();
        }
        $countByPhone = $repository->getAllCountDataByContactMethod('电话',$mid);
        $countByWeixin = $repository->getAllCountDataByContactMethod('微信',$mid);
        $countByQQ = $repository->getAllCountDataByContactMethod('QQ',$mid);
        $countByOthers = $repository->getAllCountDataByContactMethod('其它',$mid);
        
        $data = array($countByPhone,$countByWeixin,$countByQQ,$countByOthers);
        return $this->render('@CRM/ClientManage/trackingRecordsStatistics.html.twig',['data'=>$data]);
    }
    

    /**
     * top up and withdraw list
     * @Route("/fund_flow",name="fund_flow")
     * @Security("has_role('RETRIEVE_FUND_FLOW_LIST')")
     */
    public function fundFlowList(){

        return $this->render('@CRM/ClientManage/topUp_withdraw.html.twig',[]);
    }


    /**
     * 投资列表、回款列表
     * @Route("/clients_group_record_list/{type}/{mid}/{export}",name="clients_group_record_list")
     */
    public function clientsGroupRecordList($type,$mid,$export=null,Request $request){
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getManager()->getRepository('CRMBundle:Distribution');

        $output = $repository->findUidsByManager($mid,$request,true);

        $uidsString = $output['string'];

        if($export){
            $apiRes = GlobalClass::apiGetData('grouprecord',$type,$uidsString,null,null,null,false);
            if(isset($apiRes['Data']['list'])){
                $data = $apiRes['Data']['list'];
                $this->forward('AppBundle:Export:export',['data'=>$data]);
            }
        }

        if($page = $request->get('page')){
            $apiRes = GlobalClass::apiGetData('grouprecord',$type,$uidsString,$page);
        }else{
            $apiRes = GlobalClass::apiGetData('grouprecord',$type,$uidsString);
        }
        if(isset($apiRes['Data']['page'])&&isset($apiRes['Data']['list'])){
            $paginator = new CRMPaginator($request);
            $pagination = $paginator->paginate($apiRes['Data']['page']['pages']);
            $data = $apiRes['Data']['list'];
            return $this->render('@CRM/ClientManage/clientsGroupRecordList.html.twig',[
                'data'=>$data,
                'pagination'=>$pagination,
                'type'=>$type,
                'mid'=>$mid]);
        }else{
            return $this->render('@CRM/ClientManage/clientsGroupRecordList.html.twig',[]);
        }

    }


    
    /**
     * @Route("/search",name="search")
     */
    public function search(){
        if(isset($_POST['term'])){
            $em = $this->getDoctrine()->getManager();
            $term = htmlspecialchars($_POST['term']);

            $response = array(
                [
                  'uid'=>'1374',
                  'username'=>'test1234',
                  'status'=>'已认证',
                  'realname'=>'John Doe',
                  'phone'=>'189XXXXXXXX',
                  'detail'=>'detail001'
                    ]
            );

            $result = null;
            $callback = function(&$item) use($em,&$result){
                $category = $em->getRepository('CRMBundle:ClientCategory')->getCategoryByUid($item['uid']);
                $manager = $em->getRepository('UserBundle:UserInfo')->getManagerByUid($item['uid']);
                $remark = $em->getRepository('CRMBundle:ClientRemark')->findOneBy(['uid'=>$item['uid']])->getContent();
                $item['category'] = $category;
                $item['manager'] = $manager;
                $item['remark'] = $remark;

                $result.="<tr>
                        <td>{$item['uid']}</td>
                        <td>{$item['username']}</td>
                        <td>{$item['status']}</td>
                        <td>{$item['realname']}</td>
                        <td>{$item['category']}</td>
                        <td>{$item['phone']}</td>
                        <td>{$item['remark']}</td>
                        <td>{$item['manager']}</td>
                        <td>{$item['detail']}</td>
                        </tr>";

            };
            array_walk($response,$callback);

            return new Response($result);
        }else{
            return new Response('fuck Lebron!!');
        }

    }

    /**
     * @Route("/investment_analysis/{uid}",name="investment_analysis")
     */
    public function investmentAnalysisAction($uid){
        $apiRes = GlobalClass::apiGetData('investmentanalysis',null,$uid);
        $period = array();
        $earnings = array();
        $projectType = array();
        $investmentType = array();
        foreach($apiRes['period'] as $item){
            $period[$item['period']] = $item['count'];
        }
        foreach($apiRes['earnings'] as $item){
            $earnings[$item['Rate']] = $item['count'];
        }
        foreach($apiRes['projectType'] as $item){
            $projectType[$item['Type']] = $item['count'];
        }
        foreach($apiRes['investmentType'] as $item){
            if($item['IsOriginal']=='Yes'){
                $investmentType['original'] = $item['count'];
            }else{
                $investmentType['transfer'] = $item['count'];
            }
        }
        $investmentAmount = $apiRes['investmentAmount'];
        $bid = "investmentAnalysis";
        return $this->render('@CRM/ClientManage/investmentAnalysis.html.twig',[
            'period'=>$period,
            'earnings'=>$earnings,
            'investmentAmount'=>$investmentAmount,
            'projectType'=>$projectType,
            'investmentType'=>$investmentType,
            'bid'=>$bid
        ]);
    }
    

}