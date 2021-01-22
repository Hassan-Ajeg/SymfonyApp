<?php

namespace App\Security\Voter;

use PhpParser\Node\Stmt\Return_;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CAN_EDIT'])
            && $subject instanceof \App\Entity\Category;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //reécuperer l'user
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        //$attribute = CAN_EDIT ou autres
        //$subject = la catégorie en question
        switch ($attribute) {
            case 'CAN_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return $subject->getOwner() === $user;
        }

        return false;
    }
}
