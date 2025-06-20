<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // Continue seulement sur la route 'connect_google_check'
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var \League\OAuth2\Client\Provider\GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                // 1) Chercher un utilisateur par son ID Google
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);

                if ($existingUser) {
                    // Mettre à jour le refresh token s'il a changé
                    if ($accessToken->getRefreshToken()) {
                        $existingUser->setGoogleRefreshToken($accessToken->getRefreshToken());
                        $this->entityManager->flush();
                    }
                    return $existingUser;
                }

                // 2) Chercher un utilisateur par son email
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if ($user) {
                    // Mettre à jour l'utilisateur existant avec son ID Google et son token
                    $user->setGoogleId($googleUser->getId());
                    $user->setGoogleRefreshToken($accessToken->getRefreshToken());
                    $this->entityManager->flush();
                    return $user;
                }

                // 3) Créer un nouvel utilisateur s'il n'existe pas
                $newUser = new User();
                $newUser->setEmail($email);
                $newUser->setGoogleId($googleUser->getId());
                $newUser->setFirstName($googleUser->getFirstName());
                $newUser->setLastName($googleUser->getLastName());
                $newUser->setGoogleRefreshToken($accessToken->getRefreshToken());
                // Mot de passe non nécessaire pour une connexion sociale
                $newUser->setPassword(''); 

                $this->entityManager->persist($newUser);
                $this->entityManager->flush();

                return $newUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirige vers la page du compte après la connexion
        return new RedirectResponse($this->router->generate('app_account'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
} 