<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        return $this->session->set('cart', $cart);
    }

    public function add(int $id)
    {
        //retrouver le panier dans la session(tableau) , s'il n'existe pas , on prend un tableau vide
        $cart = $this->getCart();

        //voir si le produit($id) existe deja dans le tableau
        if (!array_key_exists($id, $cart)) {
            //si le produit n'existe pas dans le tableau , on met la quantité à 0 
            $cart[$id] = 0;
        }
        //sinon on incrémente la quantité de 1
        $cart[$id]++;

        //enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $quantity) {

            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $quantity;
        }
        return $total;
    }
    /**
     * 
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        //on stocke les details du produit dans un tableau avec une clé : produit et qte comme valeur
        $detailedCart = [];
        //on boucle sur tous les produits de la session
        foreach ($this->getCart() as $id => $quantity) {

            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $quantity);
        }
        return $detailedCart;
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        //suppression du produit 
        unset($cart[$id]);
        //mise à jour du panier dans la session
        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }
        //soit le produit est à 1 alors il faut simplement le supprimer 
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }
        //soit le produit est à plus de 1, alors il faut le décrementer
        $cart[$id]--;

        $this->saveCart($cart);
    }

    /**
     * 
     */
    public function empty()
    {
        $this->saveCart([]);
    }
}
