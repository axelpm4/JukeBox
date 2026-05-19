<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Review;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlbumController extends AbstractController
{
    #[Route('/album/new', name: 'album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArtistRepository $artistRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('GET')) {
            $artists = $artistRepository->findBy([], ['name' => 'ASC']);

            return $this->render('main/_album_import.html.twig', [
                'artists' => $artists,
            ]);
        }

        $title = trim((string) $request->request->get('title', ''));
        $releaseDateRaw = trim((string) $request->request->get('release_date', ''));
        $coverUrl = trim((string) $request->request->get('cover_url', ''));
        $artistId = $request->request->getInt('artist_id');
        $artistName = trim((string) $request->request->get('artist_name', ''));

        $ratingRaw = $request->request->get('rating');
        $text = trim((string) $request->request->get('text', ''));

        $rating = $ratingRaw === null ? null : (float) $ratingRaw;

        if ($title === '' || $coverUrl === '' || !$artistId || $rating === null || $rating < 1 || $rating > 5 || $text === '') {
            return $this->redirectToRoute('app_main');
        }

        /** @var Artist|null $artist */
        $artist = null;
        if ($artistName !== '') {
            $artist = $artistRepository->findOneBy(['name' => $artistName]);
            if (!$artist) {
                $artist = new Artist();
                $artist->setName($artistName);
                $em->persist($artist);
                $em->flush();
            }
        } else {
            $artist = $artistRepository->find($artistId);
        }

        if (!$artist) {
            return $this->redirectToRoute('app_main');
        }


        $album = new Album();
        $album->setTitle($title);

        if ($releaseDateRaw !== '') {
            try {
                $album->setReleaseDate(new \DateTimeImmutable($releaseDateRaw));
            } catch (\Throwable) {
                // ignore invalid date
            }
        }

        $album->setCoverUrl($coverUrl);
        $album->setArtist($artist);

        $em->persist($album);
        $em->flush();

        // Avis SQL lié à l'album
        $review = new Review();
        $review->setRating($rating);
        $review->setText($text);
        $review->setUser($user);
        $review->setAlbum($album);
        $em->persist($review);
        $em->flush();


        return $this->redirectToRoute('app_main');
    }
}

