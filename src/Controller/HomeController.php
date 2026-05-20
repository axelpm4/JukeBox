<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AlbumRepository $albumRepository, ArtistRepository $artistRepository): Response
    {
        // On récupère les albums (Doctrine chargera automatiquement les artistes et les avis liés)
        $albums = $albumRepository->findBy([], ['id' => 'DESC']);
        $artists = $artistRepository->findAll();

        return $this->render('home/index.html.twig', [
            'albums' => $albums,
            'artists' => $artists,
        ]);
    }
}