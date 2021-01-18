<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['divide'] === false) {
            return;
        }

        $builder->addModelTransformer(new CentimesTransformer);
    }
    //Préciser à Symfony de quel type déja intégré s'inspire ce type qu'on est en train de créer
    public function getParent()
    {
        //ce champ s'inscris dans la famille NumberType => herite de toutes les options de NumberType
        return NumberType::class;
    }

    //surcharge d'options
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'divide' => true
        ]);
    }
}
