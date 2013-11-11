<?php

namespace Workshop\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('plain_password', 'password', array(
                'required' => false,
            ))
            ->add('plain_password', 'repeated', array(
                'required' => false,
                'type' => 'password',
                'first_options' => array(
                    'label' => 'Password'
                ),
                'second_options' => array(
                    'label' => 'Password Again'
                ),
                'invalid_message' => 'The password fields must match.',
            ))
            ->add('locked', 'checkbox', array(
                'required' => false,
                'value' => true,
            ))
            ->add('expired', 'checkbox', array(
                'required' => false,
                'value' => true,
            ))
            ->add('roles','choice', array(
                'choices' => array(
                    'ROLE_USER' => 'Normal User',
                    'ROLE_ADMIN' => 'Backend User',
                    'ROLE_CATEGORY' => 'Backend Category Admin User',
                    'ROLE_POST' => 'Backend Post Admin User',
                    'ROLE_SUPER_ADMIN' => 'Backend Super Admin',
                ),
                'multiple' => true,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Workshop\Bundle\BackendBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workshop_bundle_backendbundle_user';
    }
}
