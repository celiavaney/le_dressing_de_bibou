<?php

namespace App\Controller\Admin;

use App\Entity\Enfants;
use App\Form\EnfantsType;
use App\Repository\EnfantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $enfant = new Enfants();
        $form = $this->createForm(EnfantsType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($enfant);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_enfants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/enfants/new.html.twig', [
            'enfant' => $enfant,
            'form' => $form,
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
        $form = $this->createForm(EnfantsType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

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
        }

        return $this->redirectToRoute('app_admin_enfants_index', [], Response::HTTP_SEE_OTHER);
    }
}
