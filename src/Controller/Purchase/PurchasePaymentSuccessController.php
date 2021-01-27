<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        //On récupère la commande
        $purchase = $purchaseRepository->find($id);
        //divers test : si il n'y a pas de purchase à payer, si le status est déja PAID , si user connecté n'est celui à qui appartient cette purchase
        if (!$purchase || ($purchase && $purchase->getUser() !== $this->getUser() || ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID))) {
            $this->addFlash('warning', "La commande n'existe pas ");
            return $this->redirectToRoute('purchase_index');
        }
        //on change le statut en PAID
        $purchase->setStatus(Purchase::STATUS_PAID);
        //on registre la commande
        $em->flush();

        //on vide le panier
        $cartService->empty();

        //redirection avec un flash vers la liste des commandes
        $this->addFlash('success', "La commande a été payée et confirmée");
        return $this->redirectToRoute("purchase_index");
    }
}
