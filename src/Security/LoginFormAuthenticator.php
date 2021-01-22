<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function supports(Request $request)
    {
        //on indique que cette méthode doit être appelée si la route = login et que le formulaire est soumis
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        // retourne les infos de connexion
        return $request->request->get('login'); //array avec 3 infos

    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // retourne l'utilisateur grace aux infos de connexion retourné par la methode precedente
        try {
            return $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException("Cette adresse email n'est pas connue");
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // verifie si le mot de passe fourni correspond bien au mot de passe de la base de données
        //$credentials['password'] => $user->getPassword()
        //Retourne vrai ou faux
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);

        if (!$isValid) {
            throw new AuthenticationException("Les informations de connexion ne correspondent pas");
        }
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //ajout de l'erreur et l'user dans les attributs de la requete
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
        $login = $request->request->get('login');
        $request->attributes->set(Security::LAST_USERNAME, $login['email']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // en cas de succès , on redirigie vers homepage
        return new RedirectResponse('/');
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        //cette methode est applelée quand un user non connecté essaye d'acceder une page qui nécissite une authentification
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
