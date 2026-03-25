<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $starChoices = array_combine(
            ['1', '2', '3', '4', '5'],
            [1, 2, 3, 4, 5],
        );

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre avis',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 20),
                ],
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Note globale',
                'choices' => $starChoices,
                'expanded' => false,
                'placeholder' => 'Choisir une note',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(min: 1, max: 5),
                ],
            ])
            ->add('ratingComfort', ChoiceType::class, [
                'label' => 'Confort',
                'choices' => $starChoices,
                'required' => false,
                'placeholder' => '—',
                'constraints' => [
                    new Assert\Range(min: 1, max: 5),
                ],
            ])
            ->add('ratingPunctuality', ChoiceType::class, [
                'label' => 'Ponctualité',
                'choices' => $starChoices,
                'required' => false,
                'placeholder' => '—',
                'constraints' => [
                    new Assert\Range(min: 1, max: 5),
                ],
            ])
            ->add('ratingStaff', ChoiceType::class, [
                'label' => 'Personnel',
                'choices' => $starChoices,
                'required' => false,
                'placeholder' => '—',
                'constraints' => [
                    new Assert\Range(min: 1, max: 5),
                ],
            ])
            ->add('ratingFood', ChoiceType::class, [
                'label' => 'Nourriture',
                'choices' => $starChoices,
                'required' => false,
                'placeholder' => '—',
                'constraints' => [
                    new Assert\Range(min: 1, max: 5),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
