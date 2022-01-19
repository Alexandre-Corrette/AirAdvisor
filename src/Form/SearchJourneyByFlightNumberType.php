<?php

namespace App\Form;

use App\Entity\Flight;
use App\Service\CallApiService;
use App\Service\SearchJourneyService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Date;

class SearchJourneyByFlightNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'form-control datepicker'],
            ])
            ->add('arrivalCity', TextType::class, [
                'label' => 'Aéroport d\'arrivée',
                'required' => false,
                'attr' => ['class' => 'form-control js-user-autocomplete']
            ])
            ->add('flightNumber', TextType::class, [
                'label' => 'Numéro de vol',
                'attr' => ['class' => 'form-control',
                'placeholder' => 'ex : 6890 '],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'GO!',
                'attr' => ['class' => 'btn btn-outline-dark w-100']
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Flight::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
