<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

    #[Route('/book')]
    final class BookController extends AbstractController
    {
    #[Route(name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository, EmpruntRepository $empruntRepository, Request $request): Response
    {
        $search = $request->query->get('search');

        $books = $search 
            ? $bookRepository->findBySearch($search) 
            : $bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books'          => $books,
            'bookRepository' => $bookRepository,
            'top5'           => $empruntRepository->findTop5DernierMois(),
            'nouveautes'     => $bookRepository->findNouveautes(),
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        $commentaire = new \App\Entity\Commentaire();
        $form = $this->createForm(\App\Form\CommentaireType::class, $commentaire);

        // Calcul moyenne
        $notes = $book->getCommentaires()->map(fn($c) => $c->getNote())->toArray();
        $moyenne = count($notes) > 0 ? round(array_sum($notes) / count($notes), 1) : null;

        return $this->render('book/show.html.twig', [
            'book'     => $book,
            'form'     => $form,
            'moyenne'  => $moyenne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/commentaire', name: 'app_book_commentaire', methods: ['POST'])]
    public function commentaire(
        int $id,
        BookRepository $bookRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour commenter.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        $commentaire = new \App\Entity\Commentaire();
        $form = $this->createForm(\App\Form\CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setBook($book);
            $commentaire->setUser($this->getUser());
            $commentaire->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($commentaire);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire ajouté !');
        }

        return $this->redirectToRoute('app_book_show', ['id' => $id]);
    }
}
