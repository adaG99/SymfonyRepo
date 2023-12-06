<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livre')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository): Response
    {
        // Affiche la liste de tous les livres
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de l'entité Livre
        $livre = new Livre();
        
        // Crée un formulaire en utilisant LivreType et le nouveau livre
        $form = $this->createForm(LivreType::class, $livre);
        
        // Traite la requête et vérifie si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste le livre dans la base de données
            $entityManager->persist($livre);
            $entityManager->flush();

            // Redirige vers la liste des livres après ajout
            return $this->redirectToRoute('app_livre_index');
        }

        // Affiche le formulaire pour créer un nouveau livre
        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        // Affiche les détails du livre
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire en utilisant LivreType et le livre existant
        $form = $this->createForm(LivreType::class, $livre);
        
        // Traite la requête et vérifie si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour le livre dans la base de données
            $entityManager->flush();

            // Redirige vers la liste des livres après modification
            return $this->redirectToRoute('app_livre_index');
        }

        // Affiche le formulaire pour modifier le livre
        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si le jeton CSRF est valide
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            // Supprime le livre de la base de données
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        // Redirige vers la liste des livres après suppression
        return $this->redirectToRoute('app_livre_index');
    }
}
