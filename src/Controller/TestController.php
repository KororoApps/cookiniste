<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController {



    /**
     * @Route("/", name="index")
     */
    public function index() {

        dd("Ca fonctionne");
    }

    /**
     * @Route("/test/{age<\d+>?0}", name="test")
     */

    public function test(Request $request, $age) {
      
        //$request = Request::createFromGlobals();
        //$age = $request->attributes->get('age');
       
        return new Response("Vous avez $age ans");
    }
}