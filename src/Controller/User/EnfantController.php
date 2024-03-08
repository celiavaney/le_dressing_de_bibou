<?php

namespace App\Controller\User;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Form\ArticlesType;
use App\Form\CategoriesType;
use App\Form\ClientArticlesType;
use App\Repository\EnfantsRepository;
use App\Repository\TaillesRepository;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/enfant')]
class EnfantController extends AbstractController
{
    #[Route('/{idEnfant}/dressing', name: 'app_user_dressing', methods: ['GET'])]
    public function dressing(EnfantsRepository $enfantsRepository, $idEnfant): Response
    {
        $enfant = $enfantsRepository->find($idEnfant);

        return $this->render('user/enfant/dressing/dressing_accueil.html.twig',[
            'enfant' => $enfant,
        ]);
    }

    #[Route('/{idEnfant}/dressing/tailles', name: 'app_user_dressing_tailles', methods: ['GET'])]
    public function dressingParTaille(EnfantsRepository $enfantsRepository, $idEnfant, ArticlesRepository $articlesRepository, TaillesRepository $taillesRepository): Response
    {
        $enfant = $enfantsRepository->find($idEnfant);

        $articleCountsBySize = [];
        $articleCountsBySizeAndCategory = [];

        foreach ($enfant->getArticles() as $article) {
            $taille = $article->getTailles()->getNom();
            $categorie = $article->getCategories()->getNom(); 
            
            if (!isset($articleCountsBySize[$taille])) {
                $articleCountsBySize[$taille] = 0;
            }
            $articleCountsBySize[$taille]++;

            if (!isset($articleCountsBySizeAndCategory[$taille][$categorie])) {
                $articleCountsBySizeAndCategory[$taille][$categorie] = 0;
            }
            $articleCountsBySizeAndCategory[$taille][$categorie]++;
        }
        // dd($articleCountsBySizeAndCategory);

        return $this->render('user/enfant/dressing/dressing_tailles.html.twig',[
            'enfant' => $enfant,
            'articleCountsBySize' => $articleCountsBySize,
            'articleCountsBySizeAndCategory' => $articleCountsBySizeAndCategory,
        ]);
    }

    #[Route('/{idEnfant}/dressing/categories', name: 'app_user_dressing_categories', methods: ['GET'])]
    public function dressingParCategorie(EnfantsRepository $enfantsRepository, $idEnfant, ArticlesRepository $articlesRepository, TaillesRepository $taillesRepository): Response
    {
        $enfant = $enfantsRepository->find($idEnfant);

        $articleCountsByCategory = [];
        $articleCountsBySizeAndCategory = [];

        foreach ($enfant->getArticles() as $article) {
            $taille = $article->getTailles()->getNom();
            $categorie = $article->getCategories()->getNom(); 
            
            if (!isset($articleCountsByCategory[$categorie])) {
                $articleCountsByCategory[$categorie] = 0;
            }
            $articleCountsByCategory[$categorie]++;

            if (!isset($articleCountsBySizeAndCategory[$categorie][$taille])) {
                $articleCountsBySizeAndCategory[$categorie][$taille] = 0;
            }
            $articleCountsBySizeAndCategory[$categorie][$taille]++;
        }
        // dd($articleCountsBySizeAndCategory);

        return $this->render('user/enfant/dressing/dressing_categories.html.twig',[
            'enfant' => $enfant,
            'articleCountsByCategory' => $articleCountsByCategory,
            'articleCountsBySizeAndCategory' => $articleCountsBySizeAndCategory,
        ]);
    }

    #[Route('/articles/{idEnfant}/ajout-article', name: 'app_user_enfant_article_new', methods: ['GET', 'POST'])]
    public function ajoutArticleEnfant(Request $request, EntityManagerInterface $entityManager,EnfantsRepository $enfantsRepository, $idEnfant): Response
    {
        $user = $this->getUser();
        $enfant = $enfantsRepository->find($idEnfant);

        $article = new Articles();
        $form = $this->createForm(ClientArticlesType::class, $article);
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

            $article->setUser($user);
            $article->setEnfants($enfant);

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash("success", "L'article a bien été ajouté.");

            return $this->redirectToRoute('app_user_enfant_index', ['id' => $idEnfant], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/enfant/articles/new.html.twig', [
            'enfant' => $enfant,
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/articles/{idEnfant}/{id}', name: 'app_user_enfant_article_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('user/enfant/articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articles/{idEnfant}/{id}/edit', name: 'app_user_enfant_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager, EnfantsRepository $enfantsRepository, $idEnfant): Response
    {
        $user = $this->getUser();
        $enfant = $enfantsRepository->find($idEnfant);

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

            $article->setUser($user);
            $article->setEnfants($enfant);

            $entityManager->flush();

            $this->addFlash("success", "L'article a bien été modifié.");

            return $this->redirectToRoute('app_user_enfant_index', ['id' => $idEnfant], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/enfant/articles/edit.html.twig', [
            'enfant' => $enfant,
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/articles/{id}', name: 'app_user_enfant_article_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash("success", "L'article a bien été supprimé.");
        }

        return $this->redirectToRoute('app_admin_enfant_index', [], Response::HTTP_SEE_OTHER);
    }
}
