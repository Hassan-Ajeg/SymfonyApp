<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas ! ");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }
    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository)
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit" , name="product_edit" )
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        //$product = new Product;
        //$product->setName('Tajine')->setPrice(123);

        //$resultat = $validator->validate($product);


        /*validation des données
        $client = [
            'nom' => 'Ajeg',
            'prenom' => 'Hassan',
            'voiture' => [
                'marque' => 'Opel',
                'couleur' => 'Noire'
            ]
        ];

        //création d'une collection de contraintes
        $collection = new Collection([
            'nom' => new NotBlank(['message' => "le nom ne doit être vide !"]),
            'prenom' => [new NotBlank(['message' => "le prénom ne doit être vide !"]), new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire moins de 3 caractères !"])],
            'voiture' => new Collection([
                'marque' => new NotBlank(['message' => 'La marque de la voiture est obligatoire']),
                'couleur' => new NotBlank(['message' => "La couleur de la voiture est obligatoire"])
            ])
        ]);*/

        //validation des données en utilisant Validator
        //$resultat = $validator->validate($client, $collection);

        /*if ($resultat->count() > 0) {
            dd("il y a des erreurs ");
        }
        dd("tout va bien ");*/

        //recherche du produit en fonction de l'id
        $product = $productRepository->find($id);

        //création du formulaire correspondant à l'entité Product
        $form = $this->createForm(ProductType::class, $product);

        //remplissage du formulaire avec les données du produit trouvé, 
        //il est possible de le passer en param au moment de la création du formulaire
        //$form->setData($product);

        //demande au formulaire d'inspecter la requete si le form est soumis
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            /*faire la redirection vers la page du produit édité
            //générer l'url de redirection
            $url = $urlGenerator->generate('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug'          => $product->getSlug()
            ]);
            //création d'une nouvelle réponse HTTP (instance de RedirectResponse) 
            $response = new RedirectResponse($url);
            return $response; */

            //en utilisant les raccourcis offerts par AbstractController
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug'          => $product->getSlug()
            ]);
        }

        //Création d'une vue du formulaire
        $formView = $form->createView();


        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }
    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product();

        //builder => configurateur de formulaire
        //$builder = $factory->createBuilder(ProductType::class, $product);

        //demande de création de formulaire, la variable $form est une classe énorme 
        //$form = $builder->getForm();

        $form = $this->createForm(ProductType::class, $product);

        //demande au formulaire de gérer la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $product = $form->getData(); cette ligne a été remplacée par l'injection du produit au moment de la création du form

            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush();

            //redirection vers la page du produit créé
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug'          => $product->getSlug()
            ]);
        }
        //création d'une vue du form => ce qui est lié à l'affichage, la vue est transmise au twig
        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
