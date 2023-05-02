<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response

    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = new Categories();
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $category->setSlug($slugger->slug($category->getName())->lower());
            $lastCategory = $categoriesRepository->findOneBy([], ['categoryOrder' => 'desc']);
            $category->setCategoryOrder($lastCategory->getCategoryOrder() + 1);
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajouté avec succès');

            return $this->redirectToRoute('admin_categories_index');
        }


        return $this->render('admin/categories/add.html.twig', [
            'categoryForm' => $categoryForm->createView()
        ]);
    }
}
