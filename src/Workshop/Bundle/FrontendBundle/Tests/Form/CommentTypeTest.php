<?php
namespace Workshop\Bundle\FrontendBundle\Test\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Workshop\Bundle\BackendBundle\Entity;
use Workshop\Bundle\FrontendBundle\Form;

class CommentTypeTest extends WebTestCase
{

    protected $container;
    protected $client;

    protected function setup()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed                    $data    The initial data for the form
     * @param array                    $options Options for the form
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * @dataProvider getTestFormData
     */
    public function testForm($data)
    {
        $commentEntity = new Entity\Comment();
        $formType = new Form\CommentType();
        $form = $this->createForm($formType, $commentEntity, array('csrf_protection' => false));
        $form->submit($data['comment']);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data['equals'] ,$form->isValid());
        $this->assertEquals($commentEntity, $form->getData());
    }

    public function getTestFormData()
    {
        return array(
            array(
                'data' => array(
                    'equals' => false,
                    'comment' => array(
                        'content' => 'test',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'equals' => false,
                    'comment' => array(
                        'name' => '',
                        'content' => 'test',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'equals' => true,
                    'comment' => array(
                        'name' => 'ricky',
                        'content' => 'test',
                    ),
                ),
            ),
        );
    }
}