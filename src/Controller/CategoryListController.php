<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use App\Form\CategoryFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;

class CategoryListController extends AbstractController
{

    /**
     * @Route("/admin/categories", name="app_categoryList")
     */
    public function categories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $user = $this->getUser();
        return $this->render('categories/categories.html.twig', array(
            'categories' => $categories));
    }

    /**
     * @Route("/admin/categories/delete/{id}", name="app_categoryDelete")
     */
    public function categoryDelete($id)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);
        $categoryName = $manager->getRepository(Category::class)->find($id)->getName();
        $event = $manager->getRepository(Event::class)->findOneBy(['category' => $category->getId()]);

        if ($category != null && !$event)
        {
            $user->unsubscribeCategory($categoryName);
            $manager->persist($user);
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
            'addcategoryform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/categories/edit/{id}", name="app_categoryEdit")
     */
    public function categoryEdit(Request $request, $id)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id);
        $oldcategoryname = $manager->getRepository(Category::class)->find($id)->getName();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newcategoryName=$form->get('name')->getData();
            $user->replaceSubscribedCategories($oldcategoryname,$newcategoryName);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_categoryList');
        }

        return $this->render('categories/category_form.html.twig', [
            'addcategoryform' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/categories/subscribe/{id}", name="app_categorySubscribe")
     */
    public function subscribeCategory($id)
    {

        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id)->getName();
        $oldCategories=$user->getSubscribedCategories();
        if($oldCategories == null)
        {
            $newCategories=$category . ',';
        }
        else{
            $newCategories=$oldCategories . $category . ',';
        }

        if(strpos($oldCategories,$category) === false) {
            $user->setSubscribedCategories($newCategories);

        }

        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute("app_categoryList");

    }
    /**
     * @Route("/admin/categories/unsubscribe/{id}", name="app_categoryUnsubscribe")
     */
    public function unsubscribeCategory($id)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id)->getName();
        $user->unsubscribeCategory($category);

        $manager->persist($user);
        $manager->flush();


        return $this->redirectToRoute("app_categoryList");

    }
}
