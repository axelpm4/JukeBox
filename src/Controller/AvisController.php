<?php

namespace App\Controller;

use App\Document\Avis;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvisController extends AbstractController
{
    #[Route('/avis/new', name: 'avis_new', methods: ['POST'])]
    public function new(Request $request, DocumentManager $dm): Response
    {
        $user = $this->getUser();
        if (!$user || !is_object($user)) {
            return $this->redirectToRoute('app_login');
        }

        $ratingRaw = $request->request->get('rating');
        $text = trim((string) $request->request->get('text', ''));

        if ($ratingRaw === null) {
            return $this->redirectToRoute('app_main');
        }

        $rating = (int) $ratingRaw;
        if ($rating < 1 || $rating > 5 || $text === '') {
            return $this->redirectToRoute('app_main');
        }

        // Symfony SQL user has getEmail() via Entity\User (or userIdentifier). We only need an email string for Mongo.
        $authorEmail = method_exists($user, 'getEmail') ? $user->getEmail() : (string) $user->getUserIdentifier();

        $avis = new Avis();
        $avis->setAuthorEmail((string) $authorEmail);
        $avis->setRating($rating);
        $avis->setText($text);
        $avis->setCreatedAt(new \DateTimeImmutable());

        $dm->persist($avis);
        $dm->flush();

        return $this->redirectToRoute('app_main');
    }
}

