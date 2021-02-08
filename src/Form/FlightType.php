<?php

namespace App\Form;

use App\Entity\Flight;
use Doctrine\DBAL\Types\ArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FlightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('flightNumber', TextType::class, [
                'label'=>'Numéro de vol',
                'attr' => ['placeholder' => 'ex: AF887'],
            ])
            ->add('departureCity', TextType::class)
            ->add('arrivalCity', TextType::class)
            ->add('comments', TextType::class, [
                'label'=>'Votre commentaire',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Flight::class,
        ]);
    }
}
