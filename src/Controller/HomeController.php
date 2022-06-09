<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(BookRepository $bookRepository, RecipeRepository $recipeRepository) {

        $books = $bookRepository->findBy([], [], 4);
        $recipes = $recipeRepository->findBy([], [], 4);

        $recipesByBookId = [];
        foreach ($books as $book) {
            $recipesByBookId[$book->getId()] = $recipeRepository->findBy(['book' => $book->getId()], [], 5);
        }

        return $this->render('home.html.twig', [
            'books' => $books,
            'recipes' => $recipes,
            'recipesByBookId' => $recipesByBookId
        ]);

    }

}