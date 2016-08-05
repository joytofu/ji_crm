<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/24/16
 * Time: 3:16 PM
 */

namespace CRMBundle\Controller;


use CRMBundle\Entity\CaseBase;
use CRMBundle\Entity\KnowledgeBase;
use CRMBundle\Entity\SiteNotice;
use CRMBundle\Form\CaseBaseType;
use CRMBundle\Form\KnowledgeBaseType;
use CRMBundle\Form\SiteNoticeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use CRMBundle\Entity\ActivityRecord;
use CRMBundle\Form\ActivityRecordType;

class HelpController extends Controller
{
    /**
     * @Route("/knowledge_base/list",name="knowledge_base_list")
     * @Security("has_role('RETRIEVE_KNOWLEDGE')")
     */
    public function knowledgeBaseList(){
        /*if(!$this->getUser()->hasRole('RETRIEVE_KNOWLEDGE')){
            return $this->response('index','你没有查看知识库的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $knowledges = $em->getRepository('CRMBundle:KnowledgeBase')->findAll();
        return $this->render('CRMBundle:Help:KnowledgeList.html.twig',['knowledges'=>$knowledges]);
    }

    /**
     * @Route("/knowledge_base/add",name="add_knowledge")
     * @Security("has_role('ADD_KNOWLEDGE')")
     */
    public function addKnowledge(Request $request){
        $user = $this->getUser();
        /*if(!$user->hasRole('ADD_KNOWLEDGE')){
            return $this->response('knowledge_base_list','你没有添加知识的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $knowledge = new KnowledgeBase();
        if($userInfo = $user->getUserInfo()){
            $knowledge->setEditor($userInfo->getId());
        }
        $form = $this->createForm(KnowledgeBaseType::class,$knowledge);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($knowledge);
            $em->flush();
            return $this->response('knowledge_base_list','添加知识成功!');
        }
        return $this->render('CRMBundle:Help:KnowledgeBase.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/knowledge_base/edit/{id}",name="edit_knowledge")
     * @Security("has_role('MODIFY_KNOWLEDGE')")
     */
    public function editKnowledge(Request $request,KnowledgeBase $knowledge){
        /*if(!$this->getUser()->hasRole('MODIFY_KNOWLEDGE')){
            return $this->response('knowledge_base_list','你没有编辑知识的权限');
        }*/
        $edit = true;
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(KnowledgeBaseType::class,$knowledge);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->response('knowledge_base_list','修改知识成功!');
        }
        return $this->render('CRMBundle:Help:KnowledgeBase.html.twig',['form'=>$form->createView(),'edit'=>$edit]);
    }


    /**
     * @Route("/knowledge_base/delete/{id}",name="delete_knowledge")
     * @Security("has_role('DELETE_KNOWLEDGE')")
     */
    public function deleteKnowledge(KnowledgeBase $knowlede){
        /*if(!$this->getUser()->hasRole('DELETE_KNOWLEDGE')){
            return $this->response('knowledge_base_list','你没有删除知识的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($knowlede);
        $em->flush();
        return $this->redirectToRoute('knowledge_base_list');
    }

    /**
     * @Route("/case_base/list",name="case_base_list")
     * @Security("has_role('RETRIEVE_CASE')")
     */
    public function caseBaseList(){
        /*if(!$this->getUser()->hasRole('RETRIEVE_CASE')){
            return $this->response('index','你没有查看案例的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $cases = $em->getRepository('CRMBundle:CaseBase')->findAll();
        return $this->render('CRMBundle:Help:CaseBaseList.html.twig',['cases'=>$cases]);
    }

    /**
     * @Route("/case_base/add",name="add_case")
     * @Security("has_role('ADD_CASE')")
     */
    public function addCase(Request $request){
        /*if(!$this->getUser()->hasRole('ADD_CASE')){
            return $this->response('case_base_list','你没有添加案例的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $case = new CaseBase();
        $form = $this->createForm(CaseBaseType::class,$case);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em->persist($case);
            $em->flush();
            return $this->response('case_base_list','添加案例成功!');
        }
        return $this->render('CRMBundle:Help:CaseBase.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/case_base/edit/{id}",name="edit_case")
     * @Security("has_role('MODIFY_CASE')")
     */
    public function editCase(Request $request,CaseBase $case){
        /*if(!$this->getUser()->hasRole('MODIFY_CASE')){
            return $this->response('case_base_list','你没有编辑案例的权限');
        }*/
        $edit = true;
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CaseBaseType::class,$case);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em->flush();
            return $this->response('case_base_list','修改案例成功!');
        }
        return $this->render('CRMBundle:Help:CaseBase.html.twig',['form'=>$form->createView(),'edit'=>$edit]);
    }

    /**
     * @Route("/case_base/delete/{id}",name="delete_case")
     * @Security("has_role('DELETE_CASE')")
     */
    public function deleteCase(CaseBase $case){
        /*if(!$this->getUser()->hasRole('DELETE_CASE')){
            return $this->response('case_base_list','你没有删除案例的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($case);
        $em->flush();
        return $this->redirectToRoute('case_base_list');
    }

    /**
     * Lists all ActivityRecord entities.
     *
     * @Route("/activity_record/list", name="activity_record_list")
     * @Method("GET")
     * @Security("has_role('RETRIEVE_ACTIVITY')")
     */
    public function activityRecordList()
    {
        /*if(!$this->getUser()->hasRole('RETRIEVE_ACTIVITY')){
            return $this->response('index','你没有查看活动记录的权限');
        }*/
        $em = $this->getDoctrine()->getManager();

        $activityRecords = $em->getRepository('CRMBundle:ActivityRecord')->findAll();

        return $this->render('@CRM/Help/ActivityRecordList.html.twig', array(
            'activityRecords' => $activityRecords,
        ));
    }

    /**
     * Creates a new ActivityRecord entity.
     *
     * @Route("/activity_record/add", name="add_activity_record")
     * @Method({"GET", "POST"})
     * @Security("has_role('ADD_ACTIVITY')")
     */
    public function addActivityRecord(Request $request)
    {
        /*if(!$this->getUser()->hasRole('ADD_ACTIVITY')){
            return $this->response('activity_record_list','你没有添加活动记录的权限');
        }*/
        $activityRecord = new ActivityRecord();
        $form = $this->createForm(ActivityRecordType::class, $activityRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activityRecord);
            $em->flush();
            return $this->response('activity_record_list','添加活动记录成功!');
        }

        return $this->render('CRMBundle:Help:ActivityRecord.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    

    /**
     * Displays a form to edit an existing ActivityRecord entity.
     *
     * @Route("/activity_record/edit/{id}", name="edit_activity_record")
     * @Method({"GET", "POST"})
     * @Security("has_role('MODIFY_ACTIVITY')")
     */
    public function editActivityRecord(Request $request, ActivityRecord $activityRecord)
    {
        /*if(!$this->getUser()->hasRole('MODIFY_ACTIVITY')){
            return $this->response('activity_record_list','你没有编辑活动记录的权限');
        }*/
        $edit = true;

        $form = $this->createForm('CRMBundle\Form\ActivityRecordType', $activityRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activityRecord);
            $em->flush();
            return $this->response('activity_record_list','修改活动记录成功!');
        }

        return $this->render('CRMBundle:Help:ActivityRecord.html.twig', array(
            'form' => $form->createView(),
            'edit'=>$edit
        ));
    }

    /**
     * Deletes a ActivityRecord entity.
     *
     * @Route("/activity_record/delete/{id}", name="activity_record_delete")
     * @Security("has_role('DELETE_ACTIVITY')")
     */
    public function deleteActivityRecord(ActivityRecord $activityRecord)
    {
        /*if(!$this->getUser()->hasRole('DELETE_ACTIVITY')){
            return $this->response('activity_record_list','你没有删除活动记录的权限');
        }*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($activityRecord);
        $em->flush();
        return $this->redirectToRoute('activity_record_list');
    }

    /**
     * @Route("/notice/list",name="notice_list")
     * @Security("has_role('RETRIEVE_NOTICE')")
     */
    public function noticeList(){
        /*if(!$this->getUser()->hasRole('RETRIEVE_NOTICE')){
            return $this->response('index','你没有查看公告的权限!');
        }*/
        $em = $this->getDoctrine()->getManager();
        $notices = $em->getRepository('CRMBundle:SiteNotice')->findAll();
        return $this->render('CRMBundle:Help:SiteNoticeList.html.twig',['notices'=>$notices]);
    }

    /**
     * @Route("/notice/add",name="add_notice")
     * @Security("has_role('ADD_NOTICE')")
     */
    public function addNotice(Request $request){
        /*if(!$this->getUser()->hasRole('ADD_NOTICE')){
            return $this->response('notice_list','你没有添加公告的权限!');
        }*/
        $em = $this->getDoctrine()->getManager();
        $notice = new SiteNotice();
        $form = $this->createForm(SiteNoticeType::class,$notice);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($notice);
            $em->flush();
            return $this->response('notice_list','添加公告成功!');
        }
        return $this->render('CRMBundle:Help:SiteNotice.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/notice/edit/{id}",name="edit_notice")
     * @Security("has_role('MODIFY_NOTICE')")
     */
    public function editNotice(Request $request,SiteNotice $notice){
        /*if(!$this->getUser()->hasRole('MODIFY_NOTICE')){
            return $this->response('notice_list','你没有编辑公告的权限!');
        }*/
        $edit = true;
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SiteNoticeType::class,$notice);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->response('notice_list','修改公告成功!');
        }
        return $this->render('CRMBundle:Help:SiteNotice.html.twig',['form'=>$form->createView(),'edit'=>$edit]);

    }

    /**
     * @Route("/notice/delete/{id}",name="delete_notice")
     * @Security("has_role('DELETE_NOTICE')")
     */
    public function deleteNotice(SiteNotice $notice){
        /*if(!$this->getUser()->hasRole('DELETE_NOTICE')){
            return $this->response('notice_list','你没有删除公告的权限!');
        }*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($notice);
        $em->flush();
        return $this->redirectToRoute('notice_list');
    }

    protected function response($urlName,$message){
        $url = $this->generateUrl($urlName);
        return new Response("<script>alert('$message');window.location.href='$url';</script>");
    }





}