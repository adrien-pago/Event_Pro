<?php

namespace App\Form;

use App\Entity\Prestation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la prestation',
                'attr' => ['placeholder' => 'Ex: Photographe']
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix de vente (€)',
                'currency' => false,
                'attr' => ['placeholder' => 'Ex: 1500.00']
            ])
            ->add('marge', MoneyType::class, [
                'label' => 'Marge réalisée (€)',
                'currency' => false,
                'attr' => ['placeholder' => 'Ex: 300.00']
            ])
            ->add('duree', TextType::class, [
                'label' => 'Durée de la prestation',
                'attr' => ['placeholder' => 'Ex: 8 heures']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
} 