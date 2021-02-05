<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ProductViewSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }

    public function sendEmail(ProductViewEvent $productViewEvent)
    {
        //création d'une instance d'Email ou TemplatedEmail (si on utilise twig) => (classes de symfony)
        // $email = new TemplatedEmail();
        // $email->from(new Address("contact@mail.com", "Infos de la boutique"))
        //     ->to("admin@mail.com")
        //     ->text("Un visiteur est en train de voir la page du produit n°" . $productViewEvent->getProduct()->getId())
        //     ->subject("Visite du produit " . $productViewEvent->getProduct()->getId())
        //     //on precise le fichier du mail
        //     ->htmlTemplate('emails/product_view.html.twig')
        //     //les données utilisées dans le mail par twig
        //     ->context([
        //         'product' => $productViewEvent->getProduct()
        //     ]);

        //envoi du mail
        // $this->mailer->send($email);
        // $this->logger->info('la page de du produit  ' . $productViewEvent->getProduct()->getId() . '   a été visité');
    }
}
