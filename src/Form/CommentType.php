<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Flight;
use App\Entity\Comment;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', textType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => 'le titre de votre commentaire'],
                'required' => true,
            ])
            ->add('comment', TextareaType::class,[
                'attr' =>[ 'class' => 'form-control']
            ])
            ->add('rate', IntegerType::class,[
                'label' => 'Note',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publiez',
                'attr' => ['class' => 'btn btn-outline-dark w-100']
            ]);;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
