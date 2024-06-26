<?php

namespace App\Controller\User;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/categories')]
class CategoriesController extends AbstractController
{
    // #[Route('/', name: 'app_admin_categories_index', methods: ['GET'])]
    // public function index(CategoriesRepository $categoriesRepository): Response
    // {
    //     return $this->render('admin/categories/index.html.twig', [
    //         'categories' => $categoriesRepository->findAll(),
    //     ]);
    // }

    #[Route('/new', name: 'app_user_categories_new', methods: ['GET', 'POST'])]
    public function new(CategoriesRepository $categoriesRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $enfants = $user->getEnfants();

        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category, ['enfants' => $enfants]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUser($user);

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash("success", "La catégorie a bien été ajoutée.");

            // return $this->redirectToRoute('app_user_enfants_index', [], Response::HTTP_SEE_OTHER);
        } 
        
        $categories = $categoriesRepository->findBy(['user' => $user], ['nom' => 'ASC']);

        return $this->render('user/categories/new.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_admin_categories_show', methods: ['GET'])]
    // public function show(Categories $category): Response
    // {
    //     return $this->render('admin/categories/show.html.twig', [
    //         'category' => $category,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_user_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé.');
            }

        $enfants = $user->getEnfants()->toArray();
        usort($enfants, function($a, $b) {
                return strcmp($a->getPrenom(), $b->getPrenom());
            });


        $form = $this->createForm(CategoriesType::class, $category, ['enfants' => $enfants]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUser($user);

            $entityManager->flush();

            return $this->redirectToRoute('app_user_categories_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/categories/edit.html.twig', [
            'categorie' => $category,
            'form' => $form,
        ]);

    }

    #[Route('/{id}', name: 'app_user_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash("success", "La catégorie a bien été supprimée.");
        }

        return $this->redirectToRoute('app_user_categories_new', [], Response::HTTP_SEE_OTHER);
    }
}
