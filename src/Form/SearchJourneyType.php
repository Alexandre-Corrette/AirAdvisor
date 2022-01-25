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
use Symfony\Component\Validator\Constraints\Date;

class SearchJourneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureCity', TextType::class, [
                'label' => "Aéroport de Départ",
                'required' => true,
                'attr' => ['class' => 'form-control js-user-autocomplete']

            ])
            ->add('arrivalCity', TextType::class, [
                'label' => 'Aéroport d\'arrivée',
                'required' => false,
                'attr' => ['class' => 'form-control js-user-autocomplete']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'GO!',
                'attr' => ['class' => 'btn btn-outline-dark w-100']
            ]);;
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
