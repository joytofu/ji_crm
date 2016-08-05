<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/1/16
 * Time: 11:24 AM
 */

namespace UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,['label'=>'姓名'])
            ->add('cellPhone',null,['label'=>'手机号码'])
            ->add('department',ChoiceType::class,[
                'label'=>'所在部门',
                'choices'=>['市场部'=>'市场部','客服部'=>'客服部'],
                'placeholder'=>'请选择部门',
                'required'=>false
            ])
            ->add('position',null,['label'=>'岗位'])
            ->add('hireDate',null,['label'=>'入职日期'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\UserInfo',
            'csrf_protection' => false,
            'cascade_validation' => true,
        ));
    }

}