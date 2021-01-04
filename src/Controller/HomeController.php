<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository)
    {
        $products = $productRepository->findBy([], [], 3);
        //les methodes du Repository
        //count([]) , find(id), findBy([],[]) , findOneBy([], []) , findAll()
        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }
}
