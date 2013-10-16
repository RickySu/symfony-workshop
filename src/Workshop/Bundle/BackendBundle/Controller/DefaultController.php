<?php

namespace Workshop\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="@BackendHome")
     * @Template()
     */
    public function indexAction()
    {        
        return array();
    }
    
    /**
     * param in url
     * @Route("/{hello}/{name}", name="@index2")
     * @Template()
     */
    public function index2Action($name, $hello)
    {        
        return array('name' => $name, 'hello' => $hello);
    }
    
    /**
     * param in GET
     * @Route("/index3", name="@index3")
     * @Template()
     */
    public function index3Action()
    {        
        $name = $this->getRequest()->get('name', 'default name');
        $hello = $this->getRequest()->get('hello', 'default hello');
        return array('name' => $name, 'hello' => $hello);
    }    
    
    /**
     * param in POST
     * @Route("/index4", name="@index4")
     * @Method({"GET"})
     * @Template()
     */
    public function index4Action()
    {        
        return array();
    }        

    /**
     * param in POST
     * @Route("/index4", name="@index4POST")
     * @Method({"POST"})
     * @Template("WorkshopBackendBundle:Default:index2.html.twig")
     */
    public function index4POSTAction()
    {   
        $name = $this->getRequest()->request->get('name');
        $hello = $this->getRequest()->request->get('hello');
        return array('name' => $name, 'hello' => $hello);
    }        
    
}
