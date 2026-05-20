<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Review;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard; // On importe le nouvel attribut
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// 1. On applique l'attribut directement sur la classe avec la route
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    // 2. Plus besoin de l'attribut #[Route] ici !
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Jukebox - Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToUrl('Retour au site', 'fas fa-arrow-left', '/');
        
        yield MenuItem::section('Gestion Musicale');
        yield MenuItem::linkToCrud('Albums', 'fas fa-music', Album::class);
        yield MenuItem::linkToCrud('Artistes', 'fas fa-microphone', Artist::class);
        
        yield MenuItem::section('Modération & Communauté');
        yield MenuItem::linkToCrud('Avis (Reviews)', 'fas fa-comments', Review::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
    }
}