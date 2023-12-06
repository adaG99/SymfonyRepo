<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Affiche la liste de toutes les catégories
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de l'entité Categorie
        $categorie = new Categorie();
        
        // Crée un formulaire en utilisant CategorieType et la nouvelle catégorie
        $form = $this->createForm(CategorieType::class, $categorie);
        
        // Traite la requête et vérifie si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste la catégorie dans la base de données
            $entityManager->persist($categorie);
            $entityManager->flush();

            // Redirige vers la liste des catégories après ajout
            return $this->redirectToRoute('app_categorie_index');
        }

        // Affiche le formulaire pour créer une nouvelle catégorie
        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        // Affiche les détails de la catégorie
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire en utilisant CategorieType et la catégorie existante
        $form = $this->createForm(CategorieType::class, $categorie);
        
        // Traite la requête et vérifie si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour la catégorie dans la base de données
            $entityManager->flush();

            // Redirige vers la liste des catégories après modification
            return $this->redirectToRoute('app_categorie_index');
        }

        // Affiche le formulaire pour modifier la catégorie
        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si le jeton CSRF est valide
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            // Supprime la catégorie de la base de données
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        // Redirige vers la liste des catégories après suppression
        return $this->redirectToRoute('app_categorie_index');
    }
}
