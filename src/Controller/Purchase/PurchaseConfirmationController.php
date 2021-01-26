<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;


    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }
    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être cconnecté pour confirmer une commande")
     */
    public function confirm(Request $request)
    {
        //1- lire les données du formulaire ( Request)

        $form = $this->createForm(CartConfirmationType::class);
        //analyse de la requete
        $form->handleRequest($request);

        //2- si le form n'est pas soumis => message flash et redirect 
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');

            return $this->redirectToRoute('cart_show');
        }
        //3- si user non connecté =>redirect (Security) 
        $user = $this->getUser();
        //==> cette verification est remplacéé par l'annotation isGranted
        // if (!$user) {
        //     throw new AccessDeniedException("Vous devez être cconnecté pour confirmer une commande");
        // }
        //4- si panier vide => message flash et redirect (CartService)
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash("warning", "vous ne pouvez confirmer une commande avec un panier vide !");

            return $this->redirectToRoute('cart_show');
        }
        //5- si le panier contient des artiles => créer une commande (purchase)
        /** @var Purchase */
        $purchase = $form->getData();

        //6- lier la commande créée au user connecté actuellement(Security)
        $purchase->setUser($user)
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

        $this->cartService->empty();
        $this->addFlash("success", "La commande a bien été enregistrée");
        return $this->redirectToRoute('purchase_index');
    }
}
