<?php

namespace App\Controller\Admin;

use App\Entity\AirlineAccount;
use App\Entity\AirlineClaim;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AirlineClaimCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return AirlineClaim::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de revendication')
            ->setEntityLabelInPlural('Demandes de revendication')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('companyName', 'Entreprise');
        yield EmailField::new('email');
        yield TelephoneField::new('phone', 'Téléphone')->hideOnIndex();
        yield AssociationField::new('airline', 'Compagnie');
        yield TextareaField::new('message')->hideOnIndex();
        yield ChoiceField::new('status', 'Statut')->setChoices([
            'En attente' => AirlineClaim::STATUS_PENDING,
            'Approuvé' => AirlineClaim::STATUS_APPROVED,
            'Refusé' => AirlineClaim::STATUS_REJECTED,
        ])->renderAsBadges([
            AirlineClaim::STATUS_PENDING => 'warning',
            AirlineClaim::STATUS_APPROVED => 'success',
            AirlineClaim::STATUS_REJECTED => 'danger',
        ]);
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
        yield DateTimeField::new('reviewedAt', 'Traité le')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approveClaim', 'Approuver')
            ->linkToRoute('admin_claim_approve', fn (AirlineClaim $c) => ['id' => $c->getId()])
            ->displayIf(fn (AirlineClaim $c) => $c->isPending())
            ->setCssClass('btn btn-success');

        $reject = Action::new('rejectClaim', 'Refuser')
            ->linkToRoute('admin_claim_reject', fn (AirlineClaim $c) => ['id' => $c->getId()])
            ->displayIf(fn (AirlineClaim $c) => $c->isPending())
            ->setCssClass('btn btn-danger');

        return $actions
            ->add(Crud::PAGE_INDEX, $approve)
            ->add(Crud::PAGE_INDEX, $reject)
            ->add(Crud::PAGE_DETAIL, $approve)
            ->add(Crud::PAGE_DETAIL, $reject);
    }

    #[Route('/admin/claim/{id}/approve', name: 'admin_claim_approve')]
    public function approve(AirlineClaim $claim, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $claim->setStatus(AirlineClaim::STATUS_APPROVED);
        $claim->setReviewedAt(new \DateTimeImmutable());

        $account = new AirlineAccount();
        $account->setEmail($claim->getEmail());
        $account->setCompanyName($claim->getCompanyName());
        $account->setPhone($claim->getPhone());
        $account->setAirline($claim->getAirline());
        $account->setIsVerified(true);

        $tempPassword = bin2hex(random_bytes(8));
        $account->setPassword($this->passwordHasher->hashPassword($account, $tempPassword));

        $claim->getAirline()->setIsVerified(true);

        $em->persist($account);
        $em->flush();

        $this->addFlash('success', sprintf(
            'Demande approuvée. Compte créé pour %s avec le mot de passe temporaire : %s',
            $claim->getEmail(),
            $tempPassword
        ));

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    #[Route('/admin/claim/{id}/reject', name: 'admin_claim_reject')]
    public function reject(AirlineClaim $claim, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $claim->setStatus(AirlineClaim::STATUS_REJECTED);
        $claim->setReviewedAt(new \DateTimeImmutable());
        $em->flush();

        $this->addFlash('info', 'Demande refusée.');

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
