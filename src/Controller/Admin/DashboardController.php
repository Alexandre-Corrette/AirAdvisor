<?php

namespace App\Controller\Admin;

use App\Entity\Flight;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $userCount = $this->em->getRepository(User::class)->count([]);
        $reviewCount = $this->em->getRepository(Review::class)->count([]);
        $flightCount = $this->em->getRepository(Flight::class)->count([]);

        $avgRating = $this->em->createQueryBuilder()
            ->select('COALESCE(AVG(r.rating), 0)')
            ->from(Review::class, 'r')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('admin/dashboard.html.twig', [
            'userCount' => $userCount,
            'reviewCount' => $reviewCount,
            'flightCount' => $flightCount,
            'avgRating' => round((float) $avgRating, 1),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('AirAdvisor Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-users');
        yield MenuItem::linkTo(AirlineCrudController::class, 'Airlines', 'fa fa-plane');
        yield MenuItem::linkTo(FlightCrudController::class, 'Flights', 'fa fa-route');
        yield MenuItem::linkTo(ReviewCrudController::class, 'Reviews', 'fa fa-star');

        yield MenuItem::section('Compagnies pro');
        yield MenuItem::linkTo(AirlineClaimCrudController::class, 'Demandes', 'fa fa-envelope');
        yield MenuItem::linkTo(AirlineAccountCrudController::class, 'Comptes compagnies', 'fa fa-building');
    }
}
