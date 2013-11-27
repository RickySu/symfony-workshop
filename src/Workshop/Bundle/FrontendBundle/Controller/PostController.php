<?php

namespace Workshop\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Workshop\Bundle\BackendBundle\Entity;
use Workshop\Bundle\FrontendBundle\Form;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/{id}-{subject}.html", name="@postView")
     * @Template()
     */
    public function viewAction(Entity\Post $post)
    {
        $comment = new Entity\Comment();
        $comment->setPost($post);
        $form = $this->createForm(new Form\CommentType(), $comment);
        $form->handleRequest($this->getRequest());
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirect($this->generateUrl('@postView', array('id' => $post->getId(), 'subject' => $post->getSubject())));
        }
        return array('post' => $post, 'form' => $form->createView());
    }

    /**
     * @Template()
     */
    public function _commentsAction(Entity\Post $post)
    {
        $currentCategory = $this->getRequest()->get('currentCategory');
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('WorkshopBackendBundle:Comment')
                ->createQueryBuilder('c')
                ->where('c.post = :post')
                ->orderBy('c.id', 'asc')
                ->getQuery()
                ->setParameter('post', $post)
                ->getResult();
        return array('comments' => $comments);
    }
}
