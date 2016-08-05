<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 7/15/16
 * Time: 9:05 AM
 */

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = new UserPassword();
        $constraints->message = '当前用户密码不正确,请重新输入!';
        $builder->add('current_password', PasswordType::class, array(
            'label' => '请输入当前密码',
            'mapped' => false,
            'constraints' => $constraints,
        ));
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'first_options' => array('label' => '请输入新密码'),
            'second_options' => array('label' => '确认密码'),
            'invalid_message' => '两次输入的密码不正确',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'csrf_protection' => false,
            'cascade_validation' => true,
            'intention'  => 'change_password',
        ));
    }
}