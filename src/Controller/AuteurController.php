<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auteur')]
class AuteurController extends AbstractController
{
    #[Route('/', name: 'app_auteur_index', methods: ['GET'])]
    public function index(AuteurRepository $auteurRepository): Response
    {
        // Affiche la liste de tous les auteurs
        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_auteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de l'entité Auteur
        $auteur = new Auteur();
        
        // Crée un formulaire en utilisant AuteurType et l'auteur nouvellement créé
        $form = $this->createForm(AuteurType::class, $auteur);
        
        // Traite la requête et vérifie si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste l'auteur dans la base de données
            $entityManager->persist($auteur);
            $entityManager->flush();

            // Redirige vers la liste des auteurs après ajout
            return $this->redirectToRoute('app_auteur_index');
        }

        // Affiche le formulaire pour créer un nouvel auteur
        return $this->render('auteur/new.html.twig', [
            'auteur' => $auteur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_auteur_show', methods: ['GET'])]
    public function show(Auteur $auteur): Response
    {
        // Affiche les détails de l'auteur
        return $this->render('auteur/show.html.twig', [
            'auteur' => $auteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_auteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire en utilisant AuteurType et l'auteur existant
        $form = $this->createForm(AuteurType::class, $auteur);
        
        // Traite la requête et vérifie si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour l'auteur dans la base de données
            $entityManager->flush();

            // Redirige vers la liste des auteurs après modification
            return $this->redirectToRoute('app_auteur_index');
        }

        // Affiche le formulaire pour modifier l'auteur
        return $this->render('auteur/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_auteur_delete', methods: ['POST'])]
    public function delete(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si le jeton CSRF est valide
        if ($this->isCsrfTokenValid('delete'.$auteur->getId(), $request->request->get('_token'))) {
            // Supprime l'auteur de la base de données
            $entityManager->remove($auteur);
            $entityManager->flush();
        }

        // Redirige vers la liste des auteurs après suppression
        return $this->redirectToRoute('app_auteur_index');
    }
}
