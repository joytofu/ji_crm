<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/26/16
 * Time: 11:58 AM
 */

namespace CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteNoticeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',null,['label'=>'标题'])
            ->add('content',null,['label'=>'内容','attr'=>['rows'=>7]])
            ->add('submit',SubmitType::class,['label'=>'确定添加'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CRMBundle\Entity\SiteNotice'
        ));
    }
}