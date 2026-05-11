<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    // On change '/main' en '/' pour que ce soit ta page d'accueil
    #[Route('/', name: 'app_main')]
    public function index(AlbumRepository $albumRepository): Response
    {
        // On récupère les albums de la base de données
        // Ici, on prend les 8 derniers par exemple
        $albums = $albumRepository->findBy([], ['id' => 'DESC'], 8);

        return $this->render('main/index.html.twig', [
            'albums' => $albums,
        ]);
    }
}