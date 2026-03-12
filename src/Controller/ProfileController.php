<?php

namespace App\Controller;

use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EmpruntRepository $empruntRepository): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $emprunts = $empruntRepository->findByUser($user->getId());

        return $this->render('profile/index.html.twig', [
            'user'     => $user,
            'emprunts' => $emprunts,
        ]);
    }
}