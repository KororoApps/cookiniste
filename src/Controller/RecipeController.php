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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function edit (BookRepository $bookRepository, $bookId, $recipeId, RecipeRepository $recipeRepository, 
    EntityManagerInterface $em, Request $request, SluggerInterface $slugger) {
        
        $book = $bookRepository->find($bookId);
        $recipe = $recipeRepository->find($recipeId);

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setSlug(strtolower($slugger->slug($recipe->getTitle())));

            $picture = $form->get('picture')->getData();       

            if($picture === null) {
                $recipe->setPicture($recipe->getPicture());;
            } else {            
                $newPictureName = md5(uniqid()).'.'.$picture->guessExtension();    
                $directory = $this->getParameter('upload_pictures_directory');   
                $picture->move($directory, $newPictureName);                      
                $recipe->setPicture($newPictureName);

            }             


            $em->flush();
            
            return $this->redirectToRoute('book_show', [
                'bookId' => $book->getId(),
                'bookSlug' => $book->getSlug(),
                'recipeId' => $recipe->getId(),
                'recipeSlug' => $recipe->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('recipe/editRecipe.html.twig', [
            'formView' => $formView,
            'recipe' => $recipe
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

        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setSlug(strtolower($slugger->slug($recipe->getTitle())));
            $recipe->setBook($book);

            $picture = $form->get('picture')->getData();

            if($picture === null) {
                $pictureName = "nopicture.jpg";
                $noPicture = $pictureName;
                $recipe->setPicture($noPicture);;
            } else {
            
                $newPictureName = md5(uniqid()).'.'.$picture->guessExtension();    
                $directory = $this->getParameter('upload_pictures_directory');   
                $picture->move($directory, $newPictureName);                      
                $recipe->setPicture($newPictureName);

            }       

            $em->persist($recipe);
            $em->flush();
            
            return $this->redirectToRoute('book_show', [
                'bookId' => $book->getId(),
                'bookSlug' => $book->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('recipe/createRecipe.html.twig', [
            'formView' => $formView
        ]);
    }

}
