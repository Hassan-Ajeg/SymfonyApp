<?php

namespace App\Form;

use App\Entity\Purchase;
use Bezhanov\Faker\Provider\Placeholder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr'  => [
                    'placeholder' => 'Nom complet pour la livraison'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse complète',
                'attr' => [
                    'placeholder' => 'Adresse complète pour la livraison'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' =>  'Code postal',
                'attr'  => [
                    'placeholder' => 'Code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' =>  'Ville',
                'attr'  => [
                    'placeholder' => 'Ville de livraison'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class
        ]);
    }
}
