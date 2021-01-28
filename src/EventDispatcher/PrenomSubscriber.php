<?php

namespace App\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PrenomSubscriber implements EventSubscriberInterface
{
    //cette methode permet de se brancher à des events
    //cette methode contient la configuration des listners, quelle methode est appelée sur quel event ?
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'addPrenomToAttributes'
        ];
    }
    public function addPrenomToAttributes()
    {
    }
}
