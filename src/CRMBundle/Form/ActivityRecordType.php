<?php

namespace CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityRecordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',null,['label'=>'活动日期'])
            ->add('detail',null,['label'=>'详细信息','attr'=>['rows'=>7]])
            ->add('status',ChoiceType::class,['label'=>'状态','choices'=>['进行中'=>'proceeding','已结束'=>'terminated'],'expanded'=>true])
            ->add('amount',null,['label'=>'活动期间投资金额'])
            ->add('investorCount',null,['label'=>'活动期间投资人数'])
            ->add('registrationCount',null,['label'=>'活动期间注册人数'])
            ->add('verificationCount',null,['label'=>'活动期间认证人数'])
            ->add('submit',SubmitType::class,['label'=>'确定添加'])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CRMBundle\Entity\ActivityRecord'
        ));
    }
}
