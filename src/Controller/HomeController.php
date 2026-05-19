<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository; // On ajoute le repository des artistes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AlbumRepository $albumRepository, ArtistRepository $artistRepository): Response
    {
        // 1. On récupère les 8 derniers albums
        $albums = $albumRepository->findBy([], ['id' => 'DESC'], 8);

        // 2. On récupère tous les artistes pour le formulaire d'ajout
        $artists = $artistRepository->findAll();

        // 3. On envoie les DEUX variables au même fichier index.html.twig
        return $this->render('home/index.html.twig', [
            'albums' => $albums,
            'artists' => $artists,
        ]);
    }
}