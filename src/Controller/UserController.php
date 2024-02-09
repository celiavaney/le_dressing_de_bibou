<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/choix-enfant', name: 'app_user_choix_enfant')]
    public function index(): Response
    {
        return $this->render('user/choix_enfant.hmtl.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
