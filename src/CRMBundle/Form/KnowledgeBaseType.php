<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/24/16
 * Time: 5:18 PM
 */

namespace CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KnowledgeBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question',null,['label'=>'问题','attr'=>['rows'=>'7']])
            ->add('answer',null,['label'=>'业务指引','attr'=>['rows'=>'7']])
            ->add('lines',null,['label'=>'解答话术要点','attr'=>['rows'=>'7']])
            ->add('remark',null,['label'=>'备注','attr'=>['rows'=>'7']])
            ->add('submit',SubmitType::class,['label'=>'提交'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CRMBundle\Entity\KnowledgeBase',
            'csrf_protection' => false,
            'cascade_validation' => true,
        ));
    }
}