<?php

namespace Workshop\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('html:contains("This is Homepage")')->count() > 0);

        if ($profile = $client->getProfile()) {
            $this->assertGreaterThanOrEqual(2, $profile->getCollector('db')->getQueryCount());
        }
    }
}
