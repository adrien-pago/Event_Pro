<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $calendars = $options['google_calendars'];

        $builder
            ->add('clientName', TextType::class, [
                'label' => 'Nom du client',
            ])
            ->add('eventName', TextType::class, [
                'label' => 'Nom de l\'événement',
            ])
            ->add('eventDate', DateType::class, [
                'label' => 'Date de l\'événement',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('isFullDay', CheckboxType::class, [
                'label'    => 'Journée complète',
                'required' => false,
            ])
            ->add('startTime', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('endTime', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'required' => false,
            ]);

        if (!empty($calendars)) {
            // Transformer les calendriers en format compatible avec ChoiceType
            $calendarChoices = [];
            foreach ($calendars as $calendar) {
                $calendarChoices[$calendar['summary']] = $calendar['id'];
            }

            $builder->add('googleCalendarId', ChoiceType::class, [
                'label' => 'Synchroniser avec Google Calendar',
                'choices' => $calendarChoices,
                'required' => false,
                'mapped' => false, // Ce champ n'est pas dans l'entité Event
                'placeholder' => 'Ne pas synchroniser',
                'help' => 'Choisissez un calendrier pour synchroniser cet événement',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'google_calendars' => [], // On initialise l'option
        ]);

        $resolver->setAllowedTypes('google_calendars', 'array');
    }
} 