<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\BookRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeController extends AbstractController
{


    /**
     * @Route("/livre/{bookId}-{bookSlug}/recette/{recipeId}-{recipeSlug}", name="recipe_show")
     */
    public function show($bookSlug, $bookId, $recipeId, $recipeSlug, RecipeRepository $recipeRepository, BookRepository $bookRepository) {

        $book = $bookRepository->findOneBy([
            'id' => $bookId,
            'slug' => $bookSlug
        ]);

        $recipe = $recipeRepository->findOneBy([
            'book' => $book,
            'id' => $recipeId,
            'slug' => $recipeSlug
        ]);

        if(!$recipe) {
            throw $this->createNotFoundException("La recette demandée n'existe pas");
        }

        return $this->render('recipe/recipeShow.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * @Route("/admin/livre/{bookId}-{bookSlug}/recette/{recipeId}-{recipeSlug}/modification", name="recipe_edit")
     */
    public function edit ($bookId, $recipeSlug, $bookSlug, RecipeRepository $recipeRepository, EntityManagerInterface $em, Request $request, SluggerInterface $slugger) {

        $recipe = $recipeRepository->find($recipeSlug);

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $recipe->setSlug(strtolower($slugger->slug($recipe->getTitle())));
            $em->flush();
            
            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('recipe/editRecipe.html.twig', [
            'formView' => $formView
        ]);
}


    /**
     * @Route("/admin/livre/{bookId}-{bookSlug}/recette/creation", name="recipe_create")
     */
    public function create ($bookId, BookRepository $bookRepository, RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger) {

        $book = $bookRepository->find($bookId);
        $recipe = new Recipe;

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $recipe->setSlug(strtolower($slugger->slug($recipe->getTitle())));
            $recipe->setBook($book);

            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('recipe/createRecipe.html.twig', [
            'formView' => $formView
        ]);
    }

}
