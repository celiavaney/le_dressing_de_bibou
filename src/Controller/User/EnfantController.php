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
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $enfant = $enfantsRepository->findOneBy(['user' => $user, 'id' => $idEnfant]);

        if (!$enfant) {
            throw $this->createNotFoundException('Enfant not found');
        }

        //récupérer les articles de l'enfant par taille
        $articleCountsBySize = [];
        $articleCountsBySizeAndCategory = [];

        foreach ($enfant->getArticles() as $article) {
            $taille = $article->getTailles()->getNom();
            $categorie = $article->getCategories()->getNom(); 
            $quantity= $article->getQuantity();
            
            if (!isset($articleCountsBySize[$taille])) {
                $articleCountsBySize[$taille] = 0;
            }
            $articleCountsBySize[$taille] += $quantity;

            if (!isset($articleCountsBySizeAndCategory[$taille][$categorie])) {
                $articleCountsBySizeAndCategory[$taille][$categorie] = 0;
            }
            $articleCountsBySizeAndCategory[$taille][$categorie] += $quantity;
        }

        //récupérer les articles de l'enfant par catégorie
        $articleCountsByCategory = [];
        $articleCountsByCategoryAndSize = [];

        foreach ($enfant->getArticles() as $article) {
            $taille = $article->getTailles()->getNom();
            $categorie = $article->getCategories()->getNom(); 
            $quantity = $article->getQuantity();
            
            if (!isset($articleCountsByCategory[$categorie])) {
                $articleCountsByCategory[$categorie] = 0;
            }
            $articleCountsByCategory[$categorie] += $quantity;

            if (!isset($articleCountsByCategoryAndSize[$categorie][$taille])) {
                $articleCountsByCategoryAndSize[$categorie][$taille] = 0;
            }
            $articleCountsByCategoryAndSize[$categorie][$taille] += $quantity;

        }

        return $this->render('user/enfant/dressing/dressing_accueil.html.twig',[
            'enfant' => $enfant,
            'articleCountsBySize' => $articleCountsBySize,
            'articleCountsBySizeAndCategory' => $articleCountsBySizeAndCategory,
            'articleCountsByCategory' => $articleCountsByCategory,
            'articleCountsByCategoryAndSize' => $articleCountsByCategoryAndSize,
        ]);
    }


    #[Route('/articles/{idEnfant}/ajout-article', name: 'app_user_enfant_article_new', methods: ['GET', 'POST'])]
    public function ajoutArticleEnfant(Request $request, EntityManagerInterface $entityManager,EnfantsRepository $enfantsRepository, $idEnfant): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $enfant = $enfantsRepository->findOneBy(['user' => $user, 'id' => $idEnfant]);

        if (!$enfant) {
            throw $this->createNotFoundException('Enfant not found');
        }

        $tailles = $enfant->getTailles()->toArray();;
        $categories = $enfant->getCategories()->toArray();;

        usort($tailles, function($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });
        
        // Sort categories alphabetically
        usort($categories, function($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });

        $article = new Articles();
        $form = $this->createForm(ClientArticlesType::class, $article, ['tailles' => $tailles, 'categories' => $categories]);
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

    #[Route('/articles/tailles/{idEnfant}/{id}', name: 'app_user_enfant_articles_by_size_show', methods: ['GET'])]
    public function showArticlesByTailles(int $idEnfant, ArticlesRepository $articleRepository, EnfantsRepository $enfantsRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $enfant = $enfantsRepository->findOneBy(['user' => $user, 'id' => $idEnfant]);

        if (!$enfant) {
            throw $this->createNotFoundException('Enfant not found');
        }

        $articles = $articleRepository->findBy(['enfants' => $enfant]);

        $articlesBySizeAndCategory = [];
        $sizeCounts = [];
        $categoryCountsBySize = [];

        foreach ($articles as $article) {
            $taille = $article->getTailles()->getNom();
            $categorie = $article->getCategories()->getNom(); 
            $quantity = $article->getQuantity();
            
            if (!isset($articlesBySizeAndCategory[$taille])) {
                $articlesBySizeAndCategory[$taille] = [];
                $sizeCounts[$taille] = 0;
            }

            if (!isset($articlesBySizeAndCategory[$taille][$categorie])) {
                $articlesBySizeAndCategory[$taille][$categorie] = [];
                $categoryCountsBySize[$taille][$categorie] = 0;
            }

            $sizeCounts[$taille] += $quantity;
            $categoryCountsBySize[$taille][$categorie] += $quantity;

            $articlesBySizeAndCategory[$taille][$categorie][] = $article;
        }

        ksort($articlesBySizeAndCategory);
        foreach ($articlesBySizeAndCategory as &$categories) {
            ksort($categories);
        }


        return $this->render('user/enfant/articles/show-by-size.html.twig', [
            'articlesBySizeAndCategory' => $articlesBySizeAndCategory,
            'enfant' => $enfant,
            'sizeCounts' => $sizeCounts,
            'categoryCountsBySize' => $categoryCountsBySize,
        ]);
    }

    #[Route('/articles/categories/{idEnfant}/{id}', name: 'app_user_enfant_articles_by_category_show', methods: ['GET'])]
    public function showArticlesByCategories(int $idEnfant, ArticlesRepository $articleRepository, EnfantsRepository $enfantsRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $enfant = $enfantsRepository->findOneBy(['user' => $user, 'id' => $idEnfant]);

        if (!$enfant) {
            throw $this->createNotFoundException('Enfant not found');
        }

        $articles = $articleRepository->findBy(['enfants' => $enfant]);

        $articlesBySizeAndCategory = [];
        $categoryCounts = [];
        $sizeCountsByCategory = [];

        foreach ($articles as $article) {
            $categorie = $article->getCategories()->getNom(); 
            $taille = $article->getTailles()->getNom();
            $quantity = $article->getQuantity();
            
            if (!isset($articlesBySizeAndCategory[$categorie])) {
                $articlesBySizeAndCategory[$categorie] = [];
                $categoryCounts[$categorie] = 0;
            }

            if (!isset($articlesBySizeAndCategory[$categorie][$taille])) {
                $articlesBySizeAndCategory[$categorie][$taille] = [];
                $sizeCountsByCategory[$categorie][$taille] = 0;
            }

            $categoryCounts[$categorie] += $quantity;
            $sizeCountsByCategory[$categorie][$taille] += $quantity;

            $articlesBySizeAndCategory[$categorie][$taille][] = $article;
        }

        ksort($articlesBySizeAndCategory);
        foreach ($articlesBySizeAndCategory as &$sizes) {
            ksort($sizes);
        }

        return $this->render('user/enfant/articles/show-by-category.html.twig', [
            'articlesBySizeAndCategory' => $articlesBySizeAndCategory,
            'enfant' => $enfant,
            'categoryCounts' => $categoryCounts,
            'sizeCountsByCategory' => $sizeCountsByCategory,
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
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $enfant = $enfantsRepository->findOneBy(['user' => $user, 'id' => $idEnfant]);
        if (!$enfant) {
            throw $this->createNotFoundException('Enfant non trouvé.');
        }

        $tailles = $enfant->getTailles()->toArray();;
        $categories = $enfant->getCategories()->toArray();;

        usort($tailles, function($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });
        
        // Sort categories alphabetically
        usort($categories, function($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });

        $form = $this->createForm(ClientArticlesType::class, $article, ['tailles' => $tailles, 'categories' => $categories]);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $article->getPhoto();
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

        return $this->redirectToRoute('app_user_enfant_index', [], Response::HTTP_SEE_OTHER);
    }
}
