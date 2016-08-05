<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 7/16/16
 * Time: 3:38 PM
 */

namespace ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class RequestInfoController extends Controller
{

    public function getRequestInfoFromAuth(Request $request){
        $credential = $this->get('app.jwt_token_authenticator')->getCredentials($request);
        $decodedToken = $this->get('lexik_jwt_authentication.encoder')->decode($credential);
        return $decodedToken;
    }

    public function getUserObj(Request $request){
        $em = $this->getDoctrine()->getManager();
        $requestInfo = $this->getRequestInfoFromAuth($request);
        $username = $requestInfo['username'];
        $user = $em->getRepository('UserBundle:User')->findOneBy(['username'=>$username]);
        return $user;
    }

    public function hasRole(Request $request,$role){
        $em = $this->getDoctrine()->getManager();
        $requestInfo = $this->getRequestInfoFromAuth($request);
        $id = $requestInfo['id'];
        $user = $em->getRepository('UserBundle:User')->find($id);
        if($user->hasRole($role)){
            return true;
        }else{
            return false;
        }
    }

    public function isSeniorExecutiveGranted(Request $request){
        $requestInfo = $this->getRequestInfoFromAuth($request);
        $roles = $requestInfo['roles'];
        if(in_array('ROLE_SUPER_ADMIN',$roles) || in_array('ROLE_ADMIN',$roles) || in_array('ROLE_SENIOR_EXECUTIVE',$roles)){
            return true;
        }else{
            return false;
        }
    }


}