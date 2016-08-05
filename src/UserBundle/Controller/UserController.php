<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/1/16
 * Time: 10:35 AM
 */

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use UserBundle\Entity\UserInfo;
use UserBundle\Form\ResetPasswordType;
use UserBundle\Form\UserInfoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use UserBundle\Form\UserType;

class UserController extends BaseController
{
    /**
     * @Route("/create_user",name="create_user")
     * @Security("has_role('ADD_USER')")
     */
    public function createUser(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $em = $this->getDoctrine()->getManager();

        //$user = $userManager->createUser();
        $user = new User();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        //all access
        $originalAccess = $this->getOriginalAccess();

        $form = $formFactory->createForm();
        $form->add('userInfo',UserInfoType::class,['label'=>'基本信息']);
        $form->add('submit',SubmitType::class,['label'=>'提交']);
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $role = $request->get('role');
            $access = $request->get('access');

            //set up role access
            switch ($role){
                case 'admin':
                    $user->setRoles('ROLE_ADMIN');
                    break;
                case 'department_manager':
                    $user->setRoles(array('ROLE_DEPARTMENT_MANAGER'));
                    break;
                case 'salesman':
                    $user->setRoles(array('ROLE_SALESMAN'));
                    break;
                case 'senior_executive':
                    $user->setRoles(array('ROLE_SENIOR_EXECUTIVE'));
                    break;
                default:
                    $user->setRoles(array('ROLE_SALESMAN'));
                    break;
            }
            
            foreach($access as $item){
                $user->addRole($item);
            }


            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            //set user object to user info
            $userInfo = $user->getUserInfo();
            $userInfo->setUser($user);

            //set parent
            if($role=='salesman'){
                $department = $userInfo->getDepartment();
                $parent = $em->getRepository('UserBundle:UserInfo')->findOneBy(['department'=>$department])->getUser();
                $user->setParent($parent);
            }
            $em->flush();
            

            if (null === $response = $event->getResponse()) {
                $redirect_url = $this->generateUrl('index');
                $response = new Response("<script>alert('创建用户成功!');window.location.href='$redirect_url';</script>");

                return $response;

            }

            //log in to the new user
            //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        }

        return $this->render('@User/Default/user.html.twig',[
            'form'=>$form->createView(),
            'originalAccess'=>$originalAccess,
            'count_access'=>count($originalAccess)
        ]);

    }

    /**
     * @Route("users_list",name="users_list")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function usersList(){
        $em = $this->getDoctrine()->getManager();
        $executives = $em->getRepository('UserBundle:UserInfo')->findBy(['position'=>'高管']);
        $managers = $em->getRepository('UserBundle:UserInfo')->findBy(['position'=>'经理']);
        $salesmen = $em->getRepository('UserBundle:UserInfo')->findBy(['position'=>'业务员']);
        $admins = $em->getRepository('UserBundle:UserInfo')->findBy(['position'=>'系统管理员']);
        return $this->render('@User/Default/usersList.html.twig',[
            'executives'=>$executives,
            'managers'=>$managers,
            'salesmen'=>$salesmen,
            'admins'=>$admins
            
        ]);
    }

    /**
     * @Route("/edit_user/{id}",name="edit_user")
     * @Security("has_role('MODIFY_USER')")
     */
    public function editUser(User $user, Request $request){
        $em = $this->getDoctrine()->getManager();
        $edit = 'edit';

        //role
        $roles = $user->getRoles();

        //all access
        $originalAccess = $this->getOriginalAccess();

        $form = $this->createForm(UserType::class,$user);
        $form->add('userInfo',UserInfoType::class,['label'=>'基本信息']);
        $form->add('submit',SubmitType::class,['label'=>'提交']);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //handle password setting
            if($user->getPlainPassword()!==0){
                $user->setPasswordRequestedAt(new \DateTime('now'));
            }
            
            $role = $roles[0];
            $access = $request->get('access');
            $role_arr = array_merge(array($role),$access);
            $user->setRoles($role_arr);
            $em->flush();
            $redirect_url = $this->generateUrl('users_list');
            return new Response("<script>alert('修改员工信息成功!');window.location.href='$redirect_url';</script>");
        }
        return $this->render('@User/Default/user.html.twig',[
            'form'=>$form->createView(),
            'roles'=>$roles,
            'originalAccess'=>$originalAccess,
            'count_access'=>count($originalAccess),
            'edit'=>$edit
        ]);

    }

    /**
     * @Route("/delete_user/{id}",name="delete_user")
     * @Security("has_role('DELETE_USER')")
     */
    public function deleteUser(UserInfo $info){
        $em = $this->getDoctrine()->getManager();
        $em->remove($info);
        $em->remove($info->getUser());
        $em->flush();
        return $this->redirectToRoute('users_list');

    }
    
    
    protected function getOriginalAccess(){
        return array(
            '用户'=>[
                '查看用户'=>'RETRIEVE_USER',
                '添加用户'=>'ADD_USER',
                '修改用户信息'=>'MODIFY_USER',
                '删除用户'=>'DELETE_USER'
            ],
            '客户分配'=>[
                '查看'=>'RETRIEVE_DISTRIBUTION',
                '修改'=>'MODIFY_DISTRIBUTION'
            ],
            '客户管理'=>[
                '添加回访记录'=>'ADD_TRACKING_RECORD',
                '编辑回访记录'=>'MODIFY_TRACKING_RECORD',
                '删除回访记录'=>'DELETE_TRACKING_RECORD',
                '查看充值/提现列表'=>'RETRIEVE_FUND_FLOW_LIST',
                '查看回款计划'=>'RETRIEVE_PAYBACK_PLAN'
            ],
            '基本数据'=>[
                '查看客户服务统计'=>'RETRIEVE_TRACKING_RECORDS_STATISTICS',
                '查看基础数据'=>'RETRIEVE_BASIC_DATA'
            ],
            '统计报表'=>[
                '查看'=>'RETRIEVE_STATEMENT'
            ],
            '营销管理'=>[
                '查看'=>'RETRIEVE_SALES_MANAGEMENT'
            ],
            '帮助'=>[
                '查看知识库'=>'RETRIEVE_KNOWLEDGE',
                '添加知识库'=>'ADD_KNOWLEDGE',
                '修改知识库'=>'MODIFY_KNOWLEDGE',
                '删除知识库'=>'DELETE_KNOWLEDGE',
                '查看案例库'=>'RETRIEVE_CASE',
                '添加案例库'=>'ADD_CASE',
                '修改案例库'=>'MODIFY_CASE',
                '删除案例库'=>'DELETE_CASE',
                '查看活动记录'=>'RETRIEVE_ACTIVITY',
                '添加活动记录'=>'ADD_ACTIVITY',
                '修改活动记录'=>'MODIFY_ACTIVITY',
                '删除活动记录'=>'DELETE_ACTIVITY',
                '查看网站公告'=>'RETRIEVE_NOTICE',
                '添加网站公告'=>'ADD_NOTICE',
                '修改网站公告'=>'MODIFY_NOTICE',
                '删除网站公告'=>'DELETE_NOTICE',
                '查看产品说明'=>'RETRIEVE_PRODUCT_DESCRIPTION',
                '添加产品说明'=>'ADD_PRODUCT_DESCRIPTION',
                '修改产品说明'=>'MODIFY_PRODUCT_DESCRIPTION',
                '删除产品说明'=>'DELETE_PRODUCT_DESCRIPTION'
            ]

        );
    }
    
    
}