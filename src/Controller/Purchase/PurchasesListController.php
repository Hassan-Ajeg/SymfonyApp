<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function index()
    {
        //verifier si l'utilisateur est connecté/ security
        /** @var User */
        $user = $this->getUser();

        // cette partie est remplacée par l'annotation isGranted
        //if (!$user) {
        //     //redirection si user non connecté / générer une url a partir du nom de la route
        //     //$url = $this->router->generate('homepage');
        //     //return new RedirectResponse($url);
        //     //lever une exception // cette exception redirige vers la page de login
        //     throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes ");
        // }
        //recuperer l'id user connecté/security


        //passer l'user connecté à twig pour afficher ses commandes
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
