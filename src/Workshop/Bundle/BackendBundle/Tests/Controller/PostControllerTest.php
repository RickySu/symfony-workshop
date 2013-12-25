<?php
namespace Workshop\Bundle\BackendBundle\Tests\Controller;

class PostControllerTest extends AbstractControllerTest
{
    public function testIndex()
    {
        $this->requireLogin('/admin/');
    }
}
