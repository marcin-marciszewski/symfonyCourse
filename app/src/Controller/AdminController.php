<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminList;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine,)
    {
    }
    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route('/su/categories', name: 'categories', methods: ['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ($this->saveCategory($category, $form, $request)) {
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('post')) {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig', ['categories' => $categories->categorylist, 'form' => $form->createView(), 'is_invalid' => $is_invalid]);
    }

    #[Route('/su/edit-category/{id}', name: 'edit_category', methods: ['GET', 'POST'])]
    public function editCategory(Category $category,  Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ($this->saveCategory($category, $form, $request)) {
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('post')) {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', ['category' => $category, 'form' => $form->createView(), 'is_invalid' => $is_invalid]);
    }

    #[Route('/su/delete-category/{id}', name: 'delete_category')]
    public function deleteCategory(Category $category): Response
    {
        $em = $this->doctrine->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('categories');
    }

    #[Route('/videos', name: 'videos')]
    public function videos(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->doctrine->getRepository(Video::class)->findAll();
        } else {
            $videos = $this->getUser()->getLikedVideos();
        }
        return $this->render('admin/videos.html.twig', ['videos' => $videos]);
    }

    #[Route('/su/upload-video', name: 'upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/su/users', name: 'users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/_all_categories.hmtl.twig', ['categories' => $categories, 'editedCategory' => $editedCategory]);
    }
}
