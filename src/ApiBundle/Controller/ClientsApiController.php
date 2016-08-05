<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 7/15/16
 * Time: 10:17 AM
 */

namespace ApiBundle\Controller;

use ApiBundle\Controller\RequestInfoController as BaseController;
use AppBundle\Security\JwtAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @Route("/api")
 */
class ClientsApiController extends BaseController
{

    /**
     * @Route("/retrieve_clients",name="api_retrieve_clients")
     */
    public function retrieveClients(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUserObj($request);
        $userInfo = $user->getUserInfo();
        if($this->isSeniorExecutiveGranted($request)){
            $mid = null;
        }else{
            $mid = $userInfo->getId();
        }
        $output = $em->getRepository('CRMBundle:Distribution')->findUidsByManager($mid,$request);
        $uids = $output['array'];

        return new JsonResponse($uids);
    }

    /**
     * @Route("/customer_service/statistics",name="api_customer_service_statistics")
     */
    public function customerServiceStatistics(Request $request){
        $em = $this->getDoctrine()->getManager();
        $requestInfo = $this->getRequestInfoFromAuth($request);
        $mid = $requestInfo['mid'];
        if($this->isSeniorExecutiveGranted($request)){
            $mid = null;
        }
        $repository = $em->getRepository('CRMBundle:TrackingRecord');
        $countByPhone = $repository->getAllCountDataByContactMethod('电话',$mid);
        $countByWeixin = $repository->getAllCountDataByContactMethod('微信',$mid);
        $countByQQ = $repository->getAllCountDataByContactMethod('QQ',$mid);
        $countByOthers = $repository->getAllCountDataByContactMethod('其它',$mid);

        $data = array('phone'=>$countByPhone,'weixin'=>$countByWeixin,'qq'=>$countByQQ,'others'=>$countByOthers);

        return new JsonResponse($data);

    }

    /**
     * @Route("/payback_plan",name="api_payback_plan")
     */
    public function paybackPlan(){

        $data = array(
            [
                'uid'=>'2',
                'username'=>'test002',
                'realname'=>'John Doe',
                'capital'=>'100000',
                'interest'=>'500',
                'sum'=>'100500',
                'availableBalance'=>'20000',
                'paybackAt'=>strtotime('2016-07-01'),
                'status'=>'逾期'
            ],
            [
                'uid'=>'4',
                'username'=>'test004',
                'realname'=>'Steph Curry',
                'capital'=>'77000',
                'interest'=>'300',
                'sum'=>'100500',
                'availableBalance'=>'10000',
                'paybackAt'=>strtotime('2016-08-02'),
                'status'=>'未到期'
            ],
            [
                'uid'=>'6',
                'username'=>'test006',
                'realname'=>'Ray Gomez',
                'capital'=>'80000',
                'interest'=>'400',
                'sum'=>'80400',
                'availableBalance'=>'15000',
                'paybackAt'=>strtotime('2016-09-02'),
                'status'=>'逾期'
            ]
        );
        return new JsonResponse($data);
    }

    /**
     * @Route("/client_detail",name="api_client_detail")
     */
    public function clientDetail(Request $request){
        $uid = $request->get('uid');
        return new Response('hh');
    }


    
}