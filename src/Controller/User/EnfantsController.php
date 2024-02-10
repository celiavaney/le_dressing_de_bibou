<?php

namespace App\Controller\User;

use App\Entity\Enfants;
use App\Form\EnfantsType;
use App\Repository\EnfantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/user')]
class EnfantsController extends AbstractController
{
    #[Route('/enfants', name: 'app_user_enfants_index', methods: ['GET'])]
    public function index(EnfantsRepository $enfantsRepository): Response
    {
        return $this->render('user/enfants/index.html.twig', [
            'enfants' => $enfantsRepository->findAll(),
        ]);
    }

    #[Route('/creation-enfant', name: 'app_user_create_enfant', methods: ['GET', 'POST'])]
    #[Route('/enfant/ajouter', name: 'app_user_enfant_new', methods: ['GET', 'POST'])]
    public function createOrNewEnfant(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $enfant = new Enfants();
        $form = $this->createForm(EnfantsType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enfant->setUser($user);

            $entityManager->persist($enfant);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_enfants_index', [], Response::HTTP_SEE_OTHER);
        }

        $routeName = $request->attributes->get('_route');
        $template = ($routeName === 'app_user_create_enfant') ? 'user/creation_enfant.html.twig' : 'user/enfants/new.html.twig';

        return $this->render($template, [
            'enfant' => $enfant,
            'form' => $form,
        ]);
    }

    #[Route('/enfant/{id}', name: 'app_user_enfant_index', methods: ['GET'])]
    public function accueilEnfant(Enfants $enfant): Response
    {
        // Ensure the child belongs to the current user
        if ($enfant->getUser() === $this->getUser()) {
            return $this->render('user/enfants/enfant/accueil_enfant.html.twig', [
                'enfant' => $enfant,
            ]);
        } else {
            // Child not found or doesn't belong to the user
            throw $this->createNotFoundException('Child not found or unauthorized access.');
        }
    }

    #[Route('/enfant/{id}', name: 'app_user_enfant_delete', methods: ['POST'])]
    public function deleteEnfant(Request $request, Enfants $enfant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enfant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($enfant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_enfants_index', [], Response::HTTP_SEE_OTHER);
    }

}