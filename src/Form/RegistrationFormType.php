<?php
// src/Form/RegistrationFormType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label'       => 'Nom complet',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom complet est requis']),
                    new Length([
                        'min'        => 2,
                        'max'        => 255,
                        'minMessage' => 'Le nom complet doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom complet ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('username', TextType::class, [
                'label'       => 'Nom d\'utilisateur',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom d\'utilisateur est requis']),
                    new Length([
                        'min'        => 3,
                        'max'        => 50,
                        'minMessage' => 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est requis']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'  => 'Mot de passe',
                'mapped' => true,
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label'  => 'Confirmer le mot de passe',
                'mapped' => true,
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var User|null $user */
            $user = $event->getData();
            if (!$user) {
                return;
            }

            if (empty($user->getPlainPassword())) {
                $form->addError(new FormError('Le mot de passe est requis'));
                return;
            }
            if (strlen($user->getPlainPassword()) < 6) {
                $form->addError(new FormError('Le mot de passe doit contenir au moins 6 caractères'));
                return;
            }
            if ($user->getPlainPassword() !== $user->getConfirmPassword()) {
                $form->addError(new FormError('Les mots de passe ne correspondent pas'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
