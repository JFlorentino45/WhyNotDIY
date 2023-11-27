<?php

namespace App\Form;

use App\Entity\AdminNotification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt')
            ->add('text')
            ->add('isSignUp')
            ->add('isComment')
            ->add('isBlog')
            ->add('identifier')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminNotification::class,
        ]);
    }
}
