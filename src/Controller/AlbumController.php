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
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un album.');
            return $this->redirectToRoute('app_home');
        }

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

        $artist = null;
        if (!empty($artistId)) {
            $artist = $artistRepository->find($artistId);
        } elseif (!empty($artistName)) {
            $artist = new Artist();
            $artist->setName($artistName);
            $em->persist($artist);
        }

        if (!$artist) {
            $this->addFlash('error', 'Vous devez sélectionner un artiste existant ou en écrire un nouveau.');
            return $this->redirectToRoute('app_home');
        }

        $album = new Album();
        $album->setTitle($title);
        $album->setCoverUrl($coverUrl);
        $album->setArtist($artist);

        if (!empty($releaseDateStr)) {
            $album->setReleaseDate(new \DateTimeImmutable($releaseDateStr));
        }

        $em->persist($album);

        if ($reviewText && $rating) {
            $review = new Review();
            $review->setText($reviewText);
            $review->setRating((float) $rating);
            $review->setCreatedAt(new \DateTimeImmutable());
            $review->setUser($user);
            $review->setAlbum($album);

            $em->persist($review);
        }

        $em->flush();

        $this->addFlash('success', 'L\'album et votre avis ont été ajoutés avec succès !');

        return $this->redirectToRoute('app_home');
    }

    // AJOUT DE LA ROUTE DE CONSULTATION DE L'ALBUM
    #[Route('/album/{id}', name: 'app_album_show', methods: ['GET'])]
    public function show(Album $album): Response
    {
        // Sécurité supplémentaire : empêche de tricher via l'URL si pas connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }
}