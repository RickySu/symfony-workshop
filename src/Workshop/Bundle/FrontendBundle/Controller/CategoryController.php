<?php

namespace Workshop\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Workshop\Bundle\BackendBundle\Entity;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Template()
     */
    public function _categoryAction()
    {
        $currentCategory = $this->getRequest()->get('currentCategory');
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('WorkshopBackendBundle:Category')
                ->createQueryBuilder('c')
                ->orderBy('c.id', 'asc')
                ->getQuery()
                ->getResult();
        return array('categories' => $categories, 'currentCategory' => $currentCategory);
    }

    /**
     * @Route("/{id}-{name}", name="@categoyIndex")
     * @Template()
     */
    public function indexAction(Entity\Category $category)
    {
        $posts = $category->getPosts();
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('WorkshopBackendBundle:Post')
                ->findBy(array('category' => $category), array('updatedAt' => 'desc'));
        return array('category' => $category, 'posts' => $posts);
    }

}
