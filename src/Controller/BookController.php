<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookController extends AbstractController
{
    
    /**
     * @Route("/livre/{bookId}-{bookSlug}", name="book_show")
     */
    
    public function show($bookSlug, BookRepository $bookRepository): Response
    {

        $book = $bookRepository->findOneBy([
            'slug' => $bookSlug
        ]);

        if(!$book) {
            throw $this->createNotFoundException("Le livre demandé n'existe pas");
        }

        return $this->render('book/bookShow.html.twig', [
            'book' => $book
        ]);

    }


/**
 * @Route("/admin/livre/{bookId}-{bookSlug}/modification", name="book_edit")
 */
public function edit(SluggerInterface $slugger, $bookId, BookRepository $bookRepository, Request $request, 
EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, RecipeRepository $recipeRepository) {

    
    $book = $bookRepository->find($bookId);

    $form = $this->createForm(BookType::class, $book);

    $form->handleRequest($request);

    if($form->isSubmitted()) {
        $book->setSlug(strtolower($slugger->slug($book->getTitle())));
        $em->flush();
        
        return $this->redirectToRoute('book', [
            'bookId' => $book->getId()
        ]);
    
    }

    $formView = $form->createView();

    return $this->render('book/editBook.html.twig', [
        'book' => $book,
        'formView' => $formView
    ]);


}



/**
 * @Route("admin/livre/creation", name="book_create") 
 */    
public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, EntityManagerInterface $em) {

    
    $book = new Book;
    $form = $this->createForm(BookType::class, $book);

    $form->handleRequest($request);

    if($form->isSubmitted()) {
        $book->setSlug(strtolower($slugger->slug($book->getTitle())));

        $em->persist($book);
        $em->flush();
        
        return $this->redirectToRoute('recipe_create', [
            'bookId' => $book->getId(),
            'bookSlug' => $book->getSlug(),
        ]);
    }
    $formView = $form->createView();

    return $this->render('book/createBook.html.twig', [
        'formView' => $formView
    ]);
}


}
