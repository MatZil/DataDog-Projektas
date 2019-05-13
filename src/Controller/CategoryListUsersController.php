<?php
/**
 * Created by PhpStorm.
 * User: Kon
 * Date: 5/10/2019
 * Time: 4:04 PM
 */

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use App\Form\CategoryFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;

class CategoryListUsersController extends AbstractController
{
    /**
     * @Route("/categories", name="app_categoryListUser")
     */
    public function categories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('categories/categoriesUser.html.twig', array(
            'categories' => $categories));
    }
    /**
     * @Route("/categories/subscribe/{id}", name="app_categorySubscribeUser")
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

        return $this->redirectToRoute("app_categoryListUser");

    }
    /**
     * @Route("/categories/unsubscribe/{id}", name="app_categoryUnsubscribeUser")
     */
    public function unsubscribeCategory($id)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $category = $manager->getRepository(Category::class)->find($id)->getName();
        $user->unsubscribeCategory($category);

        $manager->persist($user);
        $manager->flush();


        return $this->redirectToRoute("app_categoryListUser");

    }
}