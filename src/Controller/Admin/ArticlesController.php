<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\ArticlesType;
use App\Form\CategoriesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/articles')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'app_admin_articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('admin/articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get("photo")->getData();
            if($fichier){
                if($article->getPhoto()){
                    $ancienFichier = $this->getParameter("dossier_uploads") . "/" . $article->getPhoto();
                    if( file_exists( $ancienFichier ) ){
                        unlink($ancienFichier);
                    }
                }

                // on récupère le nom du fichier
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);

                // pour remplacer les caractères spéciaux interdits dans les URL, on utilise la classe AsciiSlugger
                $slugger = new AsciiSlugger();
                $nouveauNomFichier = $slugger->slug($nomFichier);
                // on ajoute un string pour éviter d'avoir des doublons
                $nouveauNomFichier .= '_' . uniqid();
                // on ajoute l'extension du fichier
                $nouveauNomFichier .= "." . $fichier->guessExtension();

                // on copie le fichier dans le dossier accessible aux navigateurs clients (paramètres mis dans les fichiers services.yaml et packages twig.yaml)
                $fichier->move($this->getParameter("dossier_uploads"), $nouveauNomFichier);
                // on modifie la propriété "couverture" de l'objet Livre
                $article->setPhoto($nouveauNomFichier);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash("success", "L'article a bien été ajouté.");

            return $this->redirectToRoute('app_admin_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('admin/articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($fichier = $form->get("photo")->getData() ){
                // on récupère le nom du fichier
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                // pour remplacer les caractères spéciaux interdits dans les URL, on utilise la classe AsciiSlugger
                $slugger = new AsciiSlugger();
                $nouveauNomFichier = $slugger->slug($nomFichier);
                // on ajoute un string pour éviter d'avoir des doublons
                $nouveauNomFichier .= '_' . uniqid();
                // on ajoute l'extension du fichier
                $nouveauNomFichier .= "." . $fichier->guessExtension();
                // on copie le fichier dans le dossier accessible aux navigateurs clients (paramètres mis dans les fichiers services.yaml et packages twig.yaml)
                $fichier->move($this->getParameter("dossier_uploads"), $nouveauNomFichier);
                // on modifie la propriété "photo" de l'objet Livre
                $article->setPhoto($nouveauNomFichier);
            }

            $entityManager->flush();

            $this->addFlash("success", "L'article a bien été modifié.");

            return $this->redirectToRoute('app_admin_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash("success", "L'article a bien été supprimé.");
        }

        return $this->redirectToRoute('app_admin_articles_index', [], Response::HTTP_SEE_OTHER);
    }
}
