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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("/livres",name="book_list")
     */
    public function list(BookRepository $bookRepository, RecipeRepository $recipeRepository): Response {

        $books = $bookRepository->findAll();

        $recipes = $recipeRepository->findBy([], [], 4);

        $recipesByBookId = [];
        foreach ($books as $book) {
            $recipesByBookId[$book->getId()] = $recipeRepository->findBy(['book' => $book->getId()], [], 5);
        }
        return $this->render('book/bookList.html.twig', [
            'books' => $books,
            'recipes' => $recipes,
            'recipesByBookId' => $recipesByBookId
        ]);
    }



    /**
     * @Route("/admin/livre/{bookId}-{bookSlug}/modification", name="book_edit")
     */
    public function edit(SluggerInterface $slugger, $bookId, BookRepository $bookRepository, Request $request, 
    EntityManagerInterface $em) {

        

        
        $book = $bookRepository->find($bookId);

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $book->setSlug(strtolower($slugger->slug($book->getTitle())));

            $cover = $form->get('cover')->getData();

            if($cover === null) {
                $book->setCover($book->getCover());;
            } else {
                $newCoverName = md5(uniqid()).'.'.$cover->guessExtension();
                $directory = $this->getParameter('upload_covers_directory');           
                $cover->move($directory, $newCoverName);    
                $book->setCover($newCoverName);         
            }         


            $em->flush();
            
            return $this->redirectToRoute('book_show', [
                'bookId' => $book->getId(),
                'bookSlug' => $book->getSlug()
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
public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, 
EntityManagerInterface $em, ValidatorInterface $validator) {

    
    $book = new Book;
    $form = $this->createForm(BookType::class, $book);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {
        $book->setSlug(strtolower($slugger->slug($book->getTitle())));

        $cover = $form->get('cover')->getData();

        if($cover === null) {
            $coverName = "nocover.jpg";
            $noCover= $coverName;
            $book->setCover($noCover);;
        } else {
            $newCoverName = md5(uniqid()).'.'.$cover->guessExtension();
            $directory = $this->getParameter('upload_covers_directory');           
            $cover->move($directory, $newCoverName);    
            $book->setCover($newCoverName);         
        }             

        $em->persist($book);
        $em->flush();

        return $this->redirectToRoute('book_show', [
            'bookId' => $book->getId(),
            'bookSlug' => $book->getSlug()
        ]);
        

    }
    $formView = $form->createView();

    return $this->render('book/createBook.html.twig', [
        'formView' => $formView
    ]);
}


}
