<?php

namespace App\Controller\Admin;

use App\Entity\Flight;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class FlightCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Flight::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('flightNumber');
        yield TextField::new('flightIataCode');
        yield AssociationField::new('airline');
        yield TextField::new('departureCity');
        yield TextField::new('departureIataCode')->hideOnIndex();
        yield TextField::new('arrivalCity');
        yield TextField::new('arrivalIataCode')->hideOnIndex();
        yield DateField::new('flightDate');
        yield DateTimeField::new('scheduledDepartureTime')->hideOnIndex();
        yield DateTimeField::new('scheduledArrivalTime')->hideOnIndex();
        yield ChoiceField::new('status')->setChoices([
            'On Time' => 'on-time',
            'Delayed' => 'delayed',
            'Cancelled' => 'cancelled',
        ])->allowMultipleChoices(false);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['flightDate' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('airline'))
            ->add('status')
            ->add('flightDate');
    }
}
