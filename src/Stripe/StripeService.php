<?php

namespace App\Stripe;

use App\Entity\Purchase;
use Doctrine\ORM\Query\Expr\Func;

class StripeService
{
    protected $secretKey;
    protected $publicKey;

    public function __construct(string $secretKey, string $publicKey)
    {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPaymentIntent(Purchase $purchase)
    {
        //mise en place de la clé d'api stripe
        \Stripe\Stripe::setApiKey($this->secretKey);
        //création d'une intention de paiement, => renvoie un objet intentPayment prend en params :le montant et devise 
        return  \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
        ]);
    }
}
