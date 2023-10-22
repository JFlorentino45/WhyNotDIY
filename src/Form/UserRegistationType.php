<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', TextType::class)
            ->add('emailAddress', EmailType::class)
            ->add('plainPassword', PasswordType::class) // we'll handle hashing later
            ->add('register', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'label' => 'Register'
            ]);
    }
}