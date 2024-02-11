<?php

namespace App\Controller\Admin;

use App\Entity\Enfants;
use App\Form\EnfantsType;
use App\Repository\EnfantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class HomeAdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('admin/home.html.twig');
    }
}
