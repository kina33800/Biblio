<?php

namespace App\Controller;

use App\Form\EmpruntType;
use App\Repository\BookRepository;
use App\Repository\EmpruntRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EmpruntController extends AbstractController
{
    #[Route('/emprunt', name: 'app_emprunt')]
    public function index(EmpruntRepository $repository): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $repository->findAll(),
        ]);
    }

    #[Route('/emprunt/stats', name: 'app_emprunt_stats')]
    public function stats(EmpruntRepository $repository): Response
    {
    return $this->render('emprunt/stats.html.twig', [
        'empruntsEnCours' => $repository->findEmpruntsEnCours(),
        'statsAuteurs' => $repository->countByAuteur(),
    ]);
    }

    #[Route('/emprunts/user/{id}', name: 'emprunts_user_history', requirements: ['id' => '\d+'])]
    public function userHistory(
        int $id,
        UserRepository $userRepository,
        EmpruntRepository $empruntRepository
        ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $emprunts = $empruntRepository->findByUser($id);

        return $this->render('emprunt/user_history.html.twig', [
            'user'     => $user,
            'emprunts' => $emprunts,

            
        ]);
    }

    #[Route('/emprunt/new/{id}', name: 'app_emprunt_new')]
    public function new(
        int $id,
        BookRepository $bookRepository,
        Request $request,
        EntityManagerInterface $entityManager
        ): Response {
        $book = $bookRepository->find($id);
        
        if (!$book) {
        throw $this->createNotFoundException('Livre introuvable.');
        }

        $form = $this->createForm(EmpruntType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
         if ($book->getStock() <= 0) {
            $this->addFlash('danger', 'Livre non disponible');
            return $this->redirectToRoute('app_book_index');
        }

        // Créer/retrouver l'utilisateur
        $user = new \App\Entity\User();
        $user->setNom($form->get('nom')->getData());
        $user->setEmail($form->get('email')->getData());
        $user->setTelephone($form->get('telephone')->getData());
        $entityManager->persist($user);

        // Créer l'emprunt
        $emprunt = new \App\Entity\Emprunt();
        $emprunt->setBook($book);
        $emprunt->setUser($user);
        $emprunt->setDateEmprunt(new \DateTimeImmutable());
        $emprunt->setStatut('en_cours');
        $emprunt->setDateRetour($form->get('dateRetour')->getData());
        $entityManager->persist($emprunt);

        // stock
        $book->setStock($book->getStock() - 1);

        $entityManager->flush();

        $this->addFlash('success', 'Emprunt enregistré !');
        return $this->redirectToRoute('app_book_index');
        }

        return $this->render('emprunt/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/emprunt/{id}/detail', name: 'app_emprunt_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EmpruntRepository $empruntRepository): Response
    {
        $emprunt = $empruntRepository->find($id);

        if (!$emprunt) {
            throw $this->createNotFoundException('Emprunt introuvable.');
        }

        return $this->render('emprunt/detail.html.twig', [
            'emprunt' => $emprunt,
        ]);
    }

    #[Route('/emprunt/{id}/rendre', name: 'app_emprunt_rendre', requirements: ['id' => '\d+'])]
    public function rendre(int $id, EmpruntRepository $empruntRepository, EntityManagerInterface $entityManager): Response
    {
        $emprunt = $empruntRepository->find($id);

        if (!$emprunt) {
            throw $this->createNotFoundException('Emprunt introuvable.');
        }

        // On remplit la date de retour et on change le statut
        $emprunt->setDateRetour(new \DateTimeImmutable());
        $emprunt->setStatut('termine');

        // On remet le stock du livre
        $emprunt->getBook()->setStock($emprunt->getBook()->getStock() + 1);

        $entityManager->flush();

        $this->addFlash('success', 'Livre rendu avec succès !');
        return $this->redirectToRoute('app_emprunt');
    }
}
