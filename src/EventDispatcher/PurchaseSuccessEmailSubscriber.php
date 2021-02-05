<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $mailer;
    protected $security;

    public function __construct(MailerInterface $mailer, Security $security)
    {
        $this->mailer = $mailer;
        $this->security = $security;
    }
    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        //recuperer l'user en ligne  //service : security
        /** @var User */
        $currentUser = $this->security->getUser();
        //recuperer la commande // PurchaseSuccessEvent contient la commande
        /** @var Purchase */
        $purchase = $purchaseSuccessEvent->getPurchase();

        //ecrire le mail // nouveau Templatedmail
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo, Votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user'  => $currentUser
            ]);
        //envoyer l'email
        $this->mailer->send($email);
    }
}
