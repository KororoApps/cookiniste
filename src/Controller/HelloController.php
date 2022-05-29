<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController {


    /**
     * @Route("/hello/{nom?World}", name="hello")
     */
    public function hello(Request $request, $nom, Calculator $calculator, Slugify $slugify, Environment $twig) {

        dump($twig);

        dump($slugify->Slugify("Hello World"));

        $tva = $calculator->calcul(100);

        dump($tva);

        return new Response("Hello $nom");


    }
}