<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    protected $calculator;
    protected $tva;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{name?world}", name="hello", methods={"GET", "POST"})
     */
    public function hello($name, LoggerInterface $logger, Calculator $calculator, Slugify $slugify)
    {
        dump($slugify->slugify("Hello world"));
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello $name");
    }
}
