<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Review;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlbumController extends AbstractController
{
    #[Route('/album/new', name: 'app_album_new', methods: ['POST'])]
    public function new(Request $request, ArtistRepository $artistRepository, EntityManagerInterface $em): Response
    {
        // 1. Vérifier si l'utilisateur est connecté (requis pour lier l'avis/review SQL)
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un album.');
            return $this->redirectToRoute('app_home');
        }

        // 2. Récupération des données brutes du formulaire HTML
        $title = $request->request->get('title');
        $releaseDateStr = $request->request->get('release_date');
        $coverUrl = $request->request->get('cover_url');
        $artistId = $request->request->get('artist_id');
        $artistName = $request->request->get('artist_name');
        
        $rating = $request->request->get('rating');
        $reviewText = $request->request->get('text');

        if (!$title || !$coverUrl) {
            $this->addFlash('error', 'Le titre et l\'URL de la cover sont obligatoires.');
            return $this->redirectToRoute('app_home');
        }

        // 3. Gestion de l'Artiste (soit sélectionné, soit créé à la volée)
        $artist = null;
        if (!empty($artistId)) {
            $artist = $artistRepository->find($artistId);
        } elseif (!empty($artistName)) {
            // Si l'artiste n'existe pas, on le crée
            $artist = new Artist();
            $artist->setName($artistName);
            $em->persist($artist);
        }

        if (!$artist) {
            $this->addFlash('error', 'Vous devez sélectionner un artiste existant ou en écrire un nouveau.');
            return $this->redirectToRoute('app_home');
        }

        // 4. Création et hydratation de l'entité Album
        $album = new Album();
        $album->setTitle($title);
        $album->setCoverUrl($coverUrl);
        $album->setArtist($artist);

        if (!empty($releaseDateStr)) {
            $album->setReleaseDate(new \DateTimeImmutable($releaseDateStr));
        }

        $em->persist($album);

        // 5. Création automatique de la Review (avis SQL lié)
        if ($reviewText && $rating) {
            $review = new Review();
            $review->setText($reviewText);
            $review->setRating((float) $rating);
            $review->setCreatedAt(new \DateTimeImmutable());
            $review->setUser($user); // Lie l'utilisateur SQL connecté
            $review->setAlbum($album); // Lie le nouvel album créé juste au-dessus

            $em->persist($review);
        }

        // 6. Sauvegarde finale globale en BDD SQL
        $em->flush();

        $this->addFlash('success', 'L\'album et votre avis ont été ajoutés avec succès !');

        return $this->redirectToRoute('app_home');
    }
}