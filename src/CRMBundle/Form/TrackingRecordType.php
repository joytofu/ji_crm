<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/7/16
 * Time: 8:52 PM
 */

namespace CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrackingRecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',null,['label'=>'内容'])
            ->add('contactMethod',ChoiceType::class,[
                'label'=>'联系方式',
                'placeholder'=>'请选择联系方式',
                'choices'=>['微信'=>'微信','QQ'=>'QQ','电话'=>'电话','其它'=>'其它']
            ])
            ->add('type',ChoiceType::class,[
                'label'=>'类型',
                'placeholder'=>'请选择内容类型',
                'choices'=>['交易咨询'=>'交易咨询','项目咨询'=>'项目咨询','项目推广'=>'项目推广','其它'=>'其它']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CRMBundle\Entity\TrackingRecord',
            'csrf_protection' => false,
            'cascade_validation' => true,
        ));
    }
}