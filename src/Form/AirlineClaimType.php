<?php

namespace App\Form;

use App\Entity\Airline;
use App\Entity\AirlineClaim;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AirlineClaimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Nom de votre entreprise',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 255)],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email professionnel',
                'help' => 'Utilisez une adresse @votre-compagnie.com pour faciliter la vérification.',
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('airline', EntityType::class, [
                'class' => Airline::class,
                'choice_label' => fn (Airline $a) => sprintf('%s (%s)', $a->getName(), $a->getIataCode()),
                'label' => 'Compagnie à revendiquer',
                'placeholder' => 'Sélectionnez une compagnie',
                'constraints' => [new Assert\NotNull()],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message (optionnel)',
                'help' => 'Précisez votre rôle dans la compagnie ou toute information utile pour la vérification.',
                'required' => false,
                'attr' => ['rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AirlineClaim::class,
        ]);
    }
}
