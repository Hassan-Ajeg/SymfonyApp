<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;
use App\Form\Type\PriceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //ajouet de champs au form
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control', 'palceholder' => 'Tapez le nom du produit'
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'class' => 'form-control', 'placeholder' => 'Tapez une description assez courte mais parlante pour le visiteur'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Tapez le prix du produit en €'
                ],
                'divisor' => 100

            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => [
                    'placeholder' => 'Tapez une URL d\'image '
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'attr'  => ['class' => 'form-control'],
                'placeholder' => '--  Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);
        //addModelTransformer => intervient au moment du passage des données pour les insérer dans l'objet Form
        //addViewTransformer => Intervient au moment que Form va afficher les données dans le formulaire

        /* Cette expresion peut être remplacée par l'attribut Divisor intégré au MoneyType
        /*  $builder->get('price')->addModelTransformer(new CentimesTransformer);

        /*$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $product = $event->getData();

            if ($product->getPrice() !== null) {
                $product->setPrice($product->getPrice() * 100);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $product = $event->getData();

            if ($product->getPrice() !== null) {
                $product->setPrice($product->getPrice() / 100);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Product */
        /*$product = $event->getData();

            /*if ($product->getId() === null) {
                $form->add('category', EntityType::class, [
                    'label' => 'Catégorie',
                    'attr'  => ['class' => 'form-control'],
                    'placeholder' => '--  Choisir une catégorie --',
                    'class' => Category::class,
                    'choice_label' => function (Category $category) {
                        return strtoupper($category->getName());
                    }
                ]);
            }
        }); */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
