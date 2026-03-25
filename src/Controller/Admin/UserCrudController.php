<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('email');
        yield TextField::new('pseudo');
        yield TextField::new('firstName');
        yield TextField::new('lastName');
        yield TextField::new('departureCity');
        yield ArrayField::new('roles');
        yield DateTimeField::new('createdAt')->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $promoteAdmin = Action::new('promoteAdmin', 'Promouvoir Admin', 'fa fa-shield')
            ->linkToRoute('admin_user_promote', fn (User $user) => ['id' => $user->getId()])
            ->displayIf(fn (User $user) => !in_array('ROLE_ADMIN', $user->getRoles()));

        return $actions
            ->add(Crud::PAGE_INDEX, $promoteAdmin);
    }

    #[Route('/admin/user/{id}/promote', name: 'admin_user_promote')]
    public function promoteAdmin(
        User $user,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator,
        Request $request,
    ): RedirectResponse {
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_values(array_unique($roles)));
            $em->flush();
            $this->addFlash('success', sprintf('%s a été promu administrateur.', $user->getEmail()));
        }

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
