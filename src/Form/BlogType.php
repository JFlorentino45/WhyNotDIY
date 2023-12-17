<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Clock\now;
use Symfony\Bundle\SecurityBundle\Security;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Validator\Constraints\Length;

class BlogType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security; 
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $blog = $event->getData();
            $user = $this->security->getUser();
            
            if (!$blog || null === $blog->getId()) {
                $blog->setCreatedBy($user);
                $blog->setCreatedAt(now());
                $blog->setEdited(false);
                $blog->setEditedAt(null);
            } else {
                $blog->setEdited(true);
                $blog->setEditedAt(now());
            }
            $blog->setHidden(false);
            $blog->setVerified(false);
        });
        
        $builder
            ->add('title', null, [
                'label' => false
            ])
            ->add('category', null, [
                'label' => false,
                'placeholder' => 'Select a Category',
            ])
            ->add('videoUrl', null, [
                'label' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^.{11}\?si=.{16}$/',
                        'message' => 'The Video URL should match the format "***********?si=****************".',
                    ]),
                ],
            ])
            ->add('text', null, [
                'label' => false,
                'constraints' => [
                    new Length([
                        'min' => 50,
                        'minMessage' => 'The text should be at least {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'app_blog_new',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
