<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserAuthenticatorInterface  $userAuthenticator,
        private LoginFormAuthenticator      $loginFormAuthenticator
    ) {}

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(LoginFormType::class);
        $form->handleRequest($request);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, FormFactoryInterface $formFactory): Response
    {
        $user = new User();
        $form = $formFactory->create(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPlainPassword())
            );
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $user->setPlainPassword(null)
                 ->setConfirmPassword(null);

            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->loginFormAuthenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method will be intercepted by the logout key on your firewall.');
    }
}
