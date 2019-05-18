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
     * @Route("/category", name="app_categoryList")
     */
    public function categories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('categories/categories.html.twig', array(
            'categories' => $categories));
    }

    /**
     * @Route("/admin/category/{id}/delete", name="app_categoryDelete")
     */
    public function categoryDelete($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);

        if ($category != null) {
            $event = $manager->getRepository(Event::class)->findOneBy(['category' => $category->getId()]);
            if ($event == null) {
                $manager->remove($category);
                $manager->flush();
                $this->addFlash('success', 'Category "' . $category->getName() . '" successfully deleted');
            } else {
                $this->addFlash('danger', 'Category is used to describe an event "' . $event->getTitle() . '"');
            }
        }

        return $this->redirectToRoute("app_categoryList");
    }

    /**
     * @Route("/admin/category/add", name="app_categoryAdd")
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
            $this->addFlash('success', 'Category "' . $category->getName() . '" successfully created');

            return $this->redirectToRoute('app_categoryList');
        }

        return $this->render('categories/category_form.html.twig', [
            'addcategoryform' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="app_categoryEdit")
     */
    public function categoryEdit(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', 'Category "' . $category->getName() . '" successfully updated');

            return $this->redirectToRoute('app_categoryList');
        }

        return $this->render('categories/category_form.html.twig', [
            'addcategoryform' => $form->createView()
        ]);
    }
    /**
     * @Route("/category/{id}/subscribe", name="app_categorySubscribe")
     */
    public function subscribeCategory($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);
        if ($category != null) {
            $user->addSubscribedCategory($category);
            $manager->persist($user);
            $manager->flush();
            // $this->addFlash('success', 'Category "'.$category->getName().'" successfully subscribed');
        }

        return $this->redirectToRoute("app_categoryList");
    }
    /**
     * @Route("/category/{id}/unsubscribe", name="app_categoryUnsubscribe")
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
            // $this->addFlash('success', 'Category "'.$category->getName().'" successfully unsubscribed');
        }

        return $this->redirectToRoute("app_categoryList");
    }
}
