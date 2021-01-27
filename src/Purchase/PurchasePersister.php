<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }
    public function storePurchase(Purchase $purchase)
    {
        //6- lier la commande créée au user connecté actuellement(Security)
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

        //persister la commande

        $this->em->persist($purchase);

        //7- lier la commande aux produits du panier (CartService)

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            //création d'une ligne de commande a partir des produits du panier
            $purchaseItem = new PurchaseItem;
            //lier la ligne de commande à la commande et ajout des infos
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->quantity)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            //8- persister la ligne de commande commande (EnitityManager)
            $this->em->persist($purchaseItem);
        }
        //enregistrer le tout dans la db
        $this->em->flush();
    }
}
