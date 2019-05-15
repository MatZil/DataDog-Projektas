<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Form\CategoryFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryListController extends AbstractController
{

    /**
     * @Route("/categories", name="app_categoryList")
     */
    public function categories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('categories/categories.html.twig', array(
            'categories' => $categories));
    }

    /**
     * @Route("/admin/categories/delete/{id}", name="app_categoryDelete")
     */
    public function categoryDelete($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);
        $event = $manager->getRepository(Event::class)->findOneBy(['category' => $category->getId()]);

        if ($category != null && !$event)
        {
            $manager->remove($category);
            $manager->flush();
        }
        else
        {
            $this->addFlash('error', 'Category is used to describe an event " ' . $event->getTitle() . ' "');
        }

        return $this->redirectToRoute("app_categoryList");
    }

    /**
     * @Route("/admin/categories/add", name="app_categoryAdd")
     */
    public function categoryAdd(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categoryList');
        }

        return $this->render('categories/category_form.html.twig', [
            'addcategoryform' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/categories/edit/{id}", name="app_categoryEdit")
     */
    public function categoryEdit(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('app_categoryList');
        }

        return $this->render('categories/category_form.html.twig', [
            'addcategoryform' => $form->createView()
        ]);
    }
    /**
     * @Route("/categories/subscribe/{id}", name="app_categorySubscribe")
     */
    public function subscribeCategory($id)
    {

        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);
        if ($user != null && $category != null) {
            $user->addSubscribedCategory($category);
            $manager->persist($user);
            $manager->flush();
        }

        return $this->redirectToRoute("app_categoryList");
    }
    /**
     * @Route("/categories/unsubscribe/{id}", name="app_categoryUnsubscribe")
     */
    public function unsubscribeCategory($id)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);
        if ($user != null && $category != null) {
            $user->removeSubscribedCategory($category);
            $manager->persist($user);
            $manager->flush();
        }

        return $this->redirectToRoute("app_categoryList");
    }
}
