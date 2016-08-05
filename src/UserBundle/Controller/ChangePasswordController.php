<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 7/15/16
 * Time: 8:44 AM
 */

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use UserBundle\Form\ChangePasswordType;
use UserBundle\Entity\User;

class ChangePasswordController extends BaseController
{
    /**
     * @Route("/change_password",name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(ChangePasswordType::class,$user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('index');
                $response = new Response("<script>alert('修改密码成功!');window.location.href='$url';</script>");
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        $url = $this->generateUrl('index');
        return new Response("<script>alert('当前密码不正确或两次输入新密码不一致,请检查后重新输入!');window.location.href='$url';</script>");
        /*return $this->render('FOSUserBundle:ChangePassword:changePassword.html.twig', array(
            'form' => $form->createView()
        ));*/

    }

    /**
     * @Route("/reset_password/{id}",name="reset_password")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function resetPassword(User $user){
        $username = $user->getUsername(); //use username as plain password
        $encodedPassword = $this->get('security.password_encoder')->encodePassword($user,$username);
        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->flush();
        return new Response("success");
    }
}