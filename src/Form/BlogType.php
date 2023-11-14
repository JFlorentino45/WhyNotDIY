<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use function Symfony\Component\Clock\now;

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
            }
            
        });
        $builder
            ->add('title')
            ->add('videoUrl')
            ->add('text')
            ->add('submit', SubmitType::class, [
                'label' => 'Create Blog',
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
