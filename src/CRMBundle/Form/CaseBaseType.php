<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/25/16
 * Time: 1:39 PM
 */

namespace CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaseBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mid',null,['label'=>'客服工号'])
            ->add('clientName',null,['label'=>'客户姓名'])
            ->add('contactMethod',ChoiceType::class,[
                'label'=>'联系方式',
                'placeholder'=>'请选择联系方式',
                'choices'=>['电话'=>'电话','短信'=>'短信','微信'=>'微信','QQ'=>'QQ','电子邮件'=>'电子邮件','其它'=>'其它']])
            ->add('cellphone',null,['label'=>'手机号码'])
            ->add('type',ChoiceType::class,[
                'label'=>'咨询类型',
                'placeholder'=>'请选择咨询类型',
                'choices'=>['交易咨询'=>'交易咨询','项目咨询'=>'项目咨询','项目推广'=>'项目推广','其它'=>'其它']
            ])
            ->add('question',null,['label'=>'问题','attr'=>['rows'=>'7']])
            ->add('answer',null,['label'=>'回答','attr'=>['rows'=>'7']])
            ->add('ifSolved',ChoiceType::class,['label'=>'是否解决客户疑问','choices'=>['是'=>true,'否'=>false]])
            ->add('assistantDepartment',ChoiceType::class,[
                'label'=>'协助部门',
                'placeholder'=>'请选择协助部门,如没有请留空',
                'required'=>false,
                'choices'=>['市场部'=>'市场部','客服部'=>'客服部','项目部'=>'项目部','技术部'=>'技术部','人事部'=>'人事部']])
            ->add('helper',null,['label'=>'协助人员'])
            ->add('remark',null,['label'=>'备注','attr'=>['rows'=>'7']])
            ->add('submit',SubmitType::class,['label'=>'确定提交'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CRMBundle\Entity\CaseBase',
            'csrf_protection' => false,
            'cascade_validation' => true,
        ));
    }
}