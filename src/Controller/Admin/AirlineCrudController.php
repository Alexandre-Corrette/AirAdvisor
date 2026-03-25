<?php

namespace App\Controller\Admin;

use App\Entity\Airline;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AirlineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Airline::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextField::new('iataCode');
        yield TextField::new('logoUrl')->hideOnIndex();
        yield TextField::new('country');
        yield SlugField::new('slug')->setTargetFieldName('name');
    }
}
