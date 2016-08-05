<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 7/14/16
 * Time: 5:03 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AuthenticationController extends Controller
{
    /*public function __construct()
    {
        if(isset($_SERVER['HTTP_ORIGIN'])){
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']} ");
        }
        header('Access-Control-Allow-Credentials: true');
        //header('Content-Type:application/x-www-form-urlencoded');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers:Authorization,Content-Type');
    }*/

    /**
     * @Route("/api/token-authentication", name="token_authentication")
     */
    public function tokenAuthentication(Request $request)
    {
        
        $username = $request->get('username');
        $password = $request->get('password');
        
        $user = $this->getDoctrine()->getRepository('UserBundle:User')
            ->findOneBy(['username' => $username]);

        if(!$user) {
            throw $this->createNotFoundException('User Not Found!');
        }

        // password check
        if(!$this->get('security.password_encoder')->isPasswordValid($user, $password)) {
            throw $this->createAccessDeniedException();
        }

        $userInfo = $user->getUserInfo();

        // Use LexikJWTAuthenticationBundle to create JWT token that hold only information about user name
        $token = $this->get('lexik_jwt_authentication.jwt_encoder')
            ->encode(array(
                'username' => $user->getUsername(),
                'name'=>$userInfo->getName(),
                'id'=>$user->getId(),
                'mid'=>$userInfo->getId(),
                'roles'=>$user->getRoles()
            ));

        return new JsonResponse(['token'=>$token]);

    }

    /**
     * @Route("/api/secure-resource", name="secure_resource")
     */
    public function secureResource(){
        $data = [
            'test' => 'test',
            'test2' => 'test2'
        ];

        return new JsonResponse($data);
    }
}