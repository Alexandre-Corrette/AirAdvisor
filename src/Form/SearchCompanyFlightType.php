<?php

namespace App\Form;

use App\Entity\Flight;
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

class SearchCompanyFlightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('flightNumber',TextType::class, [
                'label'=> 'numéro de vol',
                'attr' => [ 'placeholder' => 'Numéro de vol :'],
                'required'=> true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'GO!',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchJourneyService::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}