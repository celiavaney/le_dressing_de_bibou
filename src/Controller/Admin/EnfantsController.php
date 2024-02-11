<?php

namespace App\Controller\Admin;

use App\Entity\Enfants;
use App\Form\EnfantsType;
use App\Repository\UserRepository;
use App\Repository\EnfantsRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/enfants')]
class EnfantsController extends AbstractController
{
    #[Route('/', name: 'app_admin_enfants_index', methods: ['GET'])]
    public function index(EnfantsRepository $enfantsRepository): Response
    {
        return $this->render('admin/enfants/index.html.twig', [
            'enfants' => $enfantsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_enfants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $enfant = new Enfants();
        $users = $userRepository->findAll();
        $form = $this->createForm(EnfantsType::class, $enfant, [
            'include_user_field' => true, 
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEnfant = $entityManager->getRepository(Enfants::class)
            ->findOneBy(['prenom' => $enfant->getPrenom(), 'user' => $enfant->getUser()]);
        
        if ($existingEnfant) {
            // Child with the same name already exists for this user
            $this->addFlash("danger",'Ce client a déjà un enfant à ce nom.');
        } else {
            // Child is unique, proceed with saving
            $entityManager->persist($enfant);
            $entityManager->flush();

            $this->addFlash("success", "L'enfant a bien été ajouté.");
    
            return $this->redirectToRoute('app_admin_enfants_index', [], Response::HTTP_SEE_OTHER);
        } }

        return $this->render('admin/enfants/new.html.twig', [
            'enfant' => $enfant,
            'form' => $form,
            'users' => $users
        ]);
    }

    #[Route('/{id}', name: 'app_admin_enfants_show', methods: ['GET'])]
    public function show(Enfants $enfant): Response
    {
        return $this->render('admin/enfants/show.html.twig', [
            'enfant' => $enfant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_enfants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enfants $enfant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnfantsType::class, $enfant, [
            'include_user_field' => false, 
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash("success", "L'enfant a bien été modifié.");

            return $this->redirectToRoute('app_admin_enfants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/enfants/edit.html.twig', [
            'enfant' => $enfant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_enfants_delete', methods: ['POST'])]
    public function delete(Request $request, Enfants $enfant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enfant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($enfant);
            $entityManager->flush();

            $this->addFlash("success", "L'enfant a bien été supprimé.");
        }

        return $this->redirectToRoute('app_admin_enfants_index', [], Response::HTTP_SEE_OTHER);
    }
}
