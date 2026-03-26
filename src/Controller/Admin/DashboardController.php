<?php

namespace App\Controller\Admin;

use App\Entity\Airline;
use App\Entity\AirlineAccount;
use App\Entity\AirlineClaim;
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
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Airlines', 'fa fa-plane', Airline::class);
        yield MenuItem::linkToCrud('Flights', 'fa fa-route', Flight::class);
        yield MenuItem::linkToCrud('Reviews', 'fa fa-star', Review::class);

        yield MenuItem::section('Compagnies pro');
        yield MenuItem::linkToCrud('Demandes', 'fa fa-envelope', AirlineClaim::class);
        yield MenuItem::linkToCrud('Comptes compagnies', 'fa fa-building', AirlineAccount::class);
    }
}
