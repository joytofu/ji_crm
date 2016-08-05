<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/15/16
 * Time: 2:50 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{

    /**
     * @Route("/redirect_after_login",name="redirectAfterLogin")
     */
    public function redirectAfterLogin(){
        if($this->getUser()){
            if($this->isGranted('ROLE_USER')){
                return $this->redirectToRoute('index');
            }else{
                $redirect_url = $this->generateUrl('fos_user_security_login');
                return new Response("<script>alert('Access Denied!');window.location.href='$redirect_url';</script>");
            }    
        }
        
    }

    /**
     * @Route("/apikey")
     */
    public function testGetApiKey(){

        $key = "f3696f51132c85c605d4260c417da697";
        $api_key = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,time()."51jili",MCRYPT_MODE_ECB);
        return new Response($api_key);
    }

    
    
}