<?php

namespace App\Controller\Admin;

use App\Entity\AirlineAccount;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AirlineAccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AirlineAccount::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Compte compagnie')
            ->setEntityLabelInPlural('Comptes compagnies')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('companyName', 'Nom');
        yield EmailField::new('email');
        yield TelephoneField::new('phone', 'Téléphone')->hideOnIndex();
        yield AssociationField::new('airline', 'Compagnie');
        yield BooleanField::new('isVerified', 'Vérifié');
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
    }
}
