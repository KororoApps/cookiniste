<?php

namespace App\Controller;

use App\Form\SearchBarType;
use App\Repository\BookRepository;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
  /**
   * @Route("/recherche", name="search_result")
   */
  public function result (Request $request, BookRepository $bookRepository, RecipeRepository $recipeRepository) {
        $query = "";
      
        $books = [];
        $recipes = [];
        $recipesByBookId = [];
        

        $form = $this->createForm(SearchBarType::class, null, [
          'action' => $this->generateUrl('search_result'),
          'method' => 'GET',
        ]);
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()) {
          
          $query = $form->get('query')->getData();
        
          $books = $bookRepository->search($query);
          $recipes = $recipeRepository->search($query);

          foreach ($books as $book) {
              $recipesByBookId[$book->getId()] = $recipeRepository->findBy(['book' => $book->getId()], [], 5);
          }
        }

        return $this->render('search/resultSearch.html.twig', [
            'query' => $query,
            'books' => $books,
            'recipes' => $recipes,
            
            'recipesByBookId' => $recipesByBookId
          ]);
  }


  public function searchBar(Request $request) {


    $form = $this->createForm(SearchBarType::class, null, [
      'action' => $this->generateUrl('search_result'),
      'method' => 'GET',
    ]);

    $formView = $form->createView();

    return $this->render('search/searchBar.html.twig', [
      'formView' => $formView
    ]);

  }

}