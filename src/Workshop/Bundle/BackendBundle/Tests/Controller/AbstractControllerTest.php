<?php
namespace Workshop\Bundle\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractControllerTest extends WebTestCase
{
    protected function requireLogin($url, $method='GET')
    {
        $client = static::createClient();

        $crawler = $client->request($method, $url);

        $container = $client->getContainer();
        $redirect = $container->get('router')->generate('@BackendLogin', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertTrue($client->getResponse()->isRedirect($redirect));
    }
}
