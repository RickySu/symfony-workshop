<?php

namespace Workshop\Bundle\BackendBundle\Tests\Controller;

use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Client;

class PostControllerTest extends AbstractControllerTest
{

    public function testIndex()
    {
        $this->requireLogin('/admin/post/');
    }

    protected function requestLogin(Client $client)
    {
        $container = $client->getContainer();
        $session = $container->get('session');
        $userManager = $container->get('fos_user.user_manager');
        /* @var $userManager UserManager */

        $loginManager = $container->get('fos_user.security.login_manager');
        /* @var $loginManager LoginManager */

        $user = $userManager->findUserByUsername('ricky');
        $firewallName = $container->getParameter('fos_user.firewall_name');
        $loginManager->loginUser($firewallName, $user);
        $container->get('session')->set("_security_$firewallName", serialize($container->get('security.context')->getToken()));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    public function testIndexWithLogin()
    {
        $client = static::createClient();
        $this->requestLogin($client);
        $crawler = $client->request('GET', '/admin/post/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Post list")')->count());
    }

}
