<?php

namespace App\Controller\Admin;

use App\Entity\Tailles;
use App\Form\TaillesType;
use App\Repository\TaillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tailles')]
class TaillesController extends AbstractController
{
    #[Route('/', name: 'app_admin_tailles_index', methods: ['GET'])]
    public function index(TaillesRepository $taillesRepository): Response
    {
        return $this->render('admin/tailles/index.html.twig', [
            'tailles' => $taillesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_tailles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taille = new Tailles();
        $form = $this->createForm(TaillesType::class, $taille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taille);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_tailles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tailles/new.html.twig', [
            'taille' => $taille,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_tailles_show', methods: ['GET'])]
    public function show(Tailles $taille): Response
    {
        return $this->render('admin/tailles/show.html.twig', [
            'taille' => $taille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_tailles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tailles $taille, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaillesType::class, $taille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_tailles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tailles/edit.html.twig', [
            'taille' => $taille,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_tailles_delete', methods: ['POST'])]
    public function delete(Request $request, Tailles $taille, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taille->getId(), $request->request->get('_token'))) {
            $entityManager->remove($taille);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_tailles_index', [], Response::HTTP_SEE_OTHER);
    }
}
