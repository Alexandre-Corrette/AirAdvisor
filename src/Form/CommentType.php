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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class)
            ->add('rate', IntegerType::class)
            ->add('author', EntityType::class,[
                'class' => User::class,
                'choice_label' => 'firstName',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('flight', EntityType::class, [
                'class' => Flight::class,
                'choice_label' => 'flightNumber',
                'multiple' => false,
                'expanded' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
