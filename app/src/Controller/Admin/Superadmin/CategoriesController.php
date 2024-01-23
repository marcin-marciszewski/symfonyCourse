<?php

namespace App\Controller\Admin\Superadmin;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    public function saveCategory($category, $form, $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($request->get('category')['name']);

            $repository = $this->doctrine->getRepository(Category::class);
            $parent = $repository->find($request->get('category')['parent']);
            $category->setParent($parent);

            $em = $this->doctrine->getManager();
            $em->persist($category);
            $em->flush();
            return true;
        }

        return false;
    }
}
