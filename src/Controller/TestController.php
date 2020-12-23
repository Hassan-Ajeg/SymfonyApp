<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    public function index()
    {
        var_dump("ça fonctionne !");
        die();
    }
    /**
     * @Route("/test/{age<\d+?0}", name="test", methods:{"GET", "POST"}, host="localhost", schemes={"http", "https"})
     */
    public function test(Request $request, $age)
    {
        //pour analyser la requete, on cree une variable request
        //Request => permet de créer un objet en tenant compte du contenu des variables super-globales
        //la variable $request permet d'acceder à $_GET ...
        //les params de la requête se trouvent dans les attributes
        //il est possible d'ajouter et contraindre les params de routes avec les regexp pour controler la saisie ... 
        //exemple : /test/{age<\d+>?0} valeur par défaut et requirements 

        return new Response("Vous avez $age ans");
    }
}
