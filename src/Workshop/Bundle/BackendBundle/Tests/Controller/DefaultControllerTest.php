<?php
namespace Workshop\Bundle\BackendBundle\Tests\Controller;

class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndex()
    {
        $this->requireLogin('/admin/');
    }
}
