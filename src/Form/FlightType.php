<?php

namespace App\Form;

use App\Entity\Flight;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FlightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('departureCity', TextType::class, [
            'label' => "Aéroport de Départ",
            'required' => true,
            'attr' => ['class' => 'form-control js-user-autocomplete']

        ])
        ->add('flightDate', DateType::class, [
            'label' => 'Date de Départ',
            'required' => true,
            'widget' => 'single_text',
            // adds a class that can be selected in JavaScript
            'attr' => ['class' => 'datepicker'],
        ])
        ->add('arrivalCity', TextType::class, [
            'label' => 'Aéroport d\'arrivée',
            'required' => false,
            'attr' => ['class' => 'form-control js-user-autocomplete']
        ])
        ->add('flightNumber', HiddenType::class, [
            'label' => 'Numéro de vol : ',
            'required' => true,
            'attr' => ['class' => 'form-control', 'type' => 'hidden']
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'GO!',
            'attr' => ['class' => 'btn btn-outline-dark w-100']
        ]);;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Flight::class,
        ]);
    }
}
