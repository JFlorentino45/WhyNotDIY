<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Clock\now;
use Symfony\Bundle\SecurityBundle\Security;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;


class CommentType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security; 
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $comment = $event->getData();
            $user = $this->security->getUser();

            if (!$comment || null === $comment->getId()) {
                $comment->setCreatedBy($user);
                $comment->setCreatedAt(now());
                $comment->setEdited(false);
                $comment->setEditedAt(null);
            } else {
                $comment->setEdited(true);
                $comment->setEditedAt(now());
            }
            $comment->setHidden(false);
            $comment->setVerified(false);
        });

        $builder
            ->add('text')
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'app_blog_show',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
