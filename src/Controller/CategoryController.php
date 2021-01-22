<?php

namespace App\Controller;

use COM;
use Faker\Factory;
use App\Entity\Category;
use App\Form\CategoryType;
use PackageVersions\FallbackVersions;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /* public function renderMenuList()
    {
        //aller chercher les categories dans la bd
        $categories = $this->categoryRepository->findAll();

        //renvoyer le rendu HTML à TWIG
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }*/
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $categoryForm->createView();
        //redirection
        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }
    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, Security $security)
    {
        //cette ligne est un raccourci (grace à l'abstract controller) qui remplace la partie commentée en dessous 
        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'accéder à cette ressource");
        // $user = $security->getUser();
        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }
        // if ($security->isGranted("ROLE_ADMIN") === false) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource ! ");
        // }

        //on récupère la catégorie à editer
        $category = $categoryRepository->find($id);
        //on verifie s'il existe
        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas ");
        }
        //ceci est très specifique => si on veut interdire l'accès a certaines pages ou objets
        //plusieurs admins avec des droits differents 
        //Cette ligne remplace la partie commentée en dessous
        //$this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le proprietaire de cette catégorie !");

        //on récupère l'user, s'il est pas connecté =>redirect vers login 
        // $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute("security_login");
        // 
        // if ($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'etes pas le proprietaire de cette catégorie");
        // }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            //redirection
            return $this->redirectToRoute('product_category', [
                'slug' => $category->getSlug()
            ]);
        }
        $formView = $form->createView();
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
