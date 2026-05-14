<?php

namespace App\Controller;

use App\Document\User as MongoUser;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Attribute\Route;

final class MongoUserController extends AbstractController
{
    #[Route('/mongo/users', name: 'mongo_user_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm, PasswordHasherFactory $passwordHasherFactory): Response
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['error' => 'Invalid JSON body'], 400);
        }

        $email = $payload['email'] ?? null;
        $plainPassword = $payload['password'] ?? null;

        if (!is_string($email) || $email === '') {
            return $this->json(['error' => 'Missing/invalid "email"'], 400);
        }
        if (!is_string($plainPassword) || $plainPassword === '') {
            return $this->json(['error' => 'Missing/invalid "password"'], 400);
        }

        $user = new MongoUser();
        $user->setEmail($email);

        $hasher = $passwordHasherFactory->getPasswordHasher($user);
        $user->setPassword($hasher->hash($plainPassword));

        $roles = $payload['roles'] ?? ['ROLE_USER'];
        $user->setRoles(is_array($roles) ? array_values($roles) : ['ROLE_USER']);

        $dm->persist($user);
        $dm->flush();

        return new JsonResponse([
            'id' => (string) $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], 201);
    }
}

