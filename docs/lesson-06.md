Unit Test And Functional Test
==============================

1) PHPUnit 安裝
---------------

在 PHP 上要作單元測試就不得不提到 PHPUnit 這套測試框架。

最快速簡單的安裝方法就是直接下載打包好的 PHAR

```
wget https://phar.phpunit.de/phpunit.phar
php phpunit.phar
```

或是透過 Composer

```JSON
{
    "require-dev": {
        "phpunit/phpunit": "3.7.*"
    }
}
```

透過 PEAR

```
pear config-set auto_discover 1
pear install pear.phpunit.de/PHPUnit
```

2) Unit Test
---------------

什麼是 Unit Test?

Unit 指的是程式中的 Class 或是 Function。

Test 則是用自動化的方式去測試這些 Class 或是 Function 是否執行正確。

3) Test Driven Development
--------------------------

在尚未實作功能之前，先將測試寫好。接著逐一實作每項功能去通過測試。

4) 實作 Unit Test
-----------------

假設我們要作一個實現加減乘除的 Class。

首先定義好 API 界面。

src/Workshop/Bundle/FrontendBundle/Utils/Calculator.php

```php
<?php
namespace Workshop\Bundle\FrontendBundle\Utils;

class Calculator
{

    public function __construct($initial = 0)
    {
    }

    public function set($val)
    {
    }

    public function reset()
    {
    }

    public function getResult()
    {
    }

    public function add($val)
    {
    }

    public function sub($val)
    {
    }

    public function mul($val)
    {
    }

    public function div($val)
    {
    }
}
```

接著我們開始撰寫測試

Symfony的慣例是將各個測試放在 Bundle 底下的 Test 目錄裡面。

例如我們的 Calculator 放在 FrontendBundle/Utils/Calculator.php

對應的 Test 就是在 Test/Utils/CalculatorTest.php

src/Workshop/Bundle/FrontendBundle/Tests/Utils/CalculatorTest.php

```php
<?php
namespace Workshop\Bundle\FrontendBundle\Tests\Utils;

use Workshop\Bundle\FrontendBundle\Utils\Calculator;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $calculator = new Calculator();
        $this->assertEquals(0, $calculator->getResult());
        $calculator = new Calculator(100);
        $this->assertEquals(100, $calculator->getResult());
    }

    public function testSet()
    {
        $calculator = new Calculator();
        $calculator->set(123);
        $this->assertEquals(123, $calculator->getResult());
    }

    public function testReset()
    {
        $calculator = new Calculator(123);
        $calculator->reset();
        $this->assertEquals(0, $calculator->getResult());
    }

    public function testAdd()
    {
        $calculator = new Calculator(123);
        $calculator->add(456);
        $this->assertEquals(123+456, $calculator->getResult());
    }

    public function testSub()
    {
        $calculator = new Calculator(123);
        $calculator->sub(456);
        $this->assertEquals(123-456, $calculator->getResult());
    }

    public function testMul()
    {
        $calculator = new Calculator(123);
        $calculator->mul(456);
        $this->assertEquals(123*456, $calculator->getResult());
    }

    public function testDiv()
    {
        $calculator = new Calculator(123);
        $calculator->div(456);
        $this->assertEquals(123/456, $calculator->getResult());
    }

}
```

接著實作一個 CalculatorTest 繼承自 \PHPUnit_Framework_TestCase

當我們執行 PHPUnit 時，PHPUnit 會自動執行所有的 testXXXX 的 public method。

上面的測試就是逐一的執行 Calculator 的各個功能，並且根據預期的回傳值去作驗證。

接著我們再將 Calculator.php 的每個功能予以實作出來。

src/Workshop/Bundle/FrontendBundle/Utils/Calculator.php

```php
<?php
namespace Workshop\Bundle\FrontendBundle\Utils;

class Calculator
{
    protected $result;

    public function __construct($initial = 0)
    {
        $this->result = $initial;
    }

    public function set($val)
    {
        $this->result = $val;
    }

    public function reset()
    {
        $this->set(0);
    }

    public function getResult()
    {
        return $this->result;
    }

    public function add($val)
    {
        $this->result+=$val;
    }

    public function sub($val)
    {
        $this->result-=$val;
    }

    public function mul($val)
    {
        $this->result*=$val;
    }

    public function div($val)
    {
        $this->result/=$val;
    }
}
```

實作完後，我們就開始跑測試

```
phpunit -c app src/Workshop/Bundle/FrontendBundle/Tests/Utils/CalculatorTest.php

PHPUnit 3.6.12 by Sebastian Bergmann.

Configuration read from /home/ricky/php/demo/symfony-workshop/app/phpunit.xml.dist

.......

Time: 0 seconds, Memory: 3.50Mb

OK (7 tests, 8 assertions)
```

如果沒有意外，那應該會看到OK的字樣，那如果實作有錯，那就會得到 FAILURES 的訊息。

5) Funtional Test
-----------------

什麼是 Functional Test?

所 Functional Test 指的是一整個完整 Request 的結果測試。

在 Symfony 中可以透過 Functional Test，實際發出一個 GET/POST，並取得 Response。

透過分析 status code，或是 html 內容來測試某個 Controller。

例如我們要測試首頁是否正常

src/Workshop/Bundle/FrontendBundle/Tests/Controller/DefaultControllerTest.php

```php
<?php

namespace Workshop\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($crawler->filter('html:contains("This is Homepage")')->count() > 0);

        if ($profile = $client->getProfile()) {
            $this->assertGreaterThanOrEqual(2, $profile->getCollector('db')->getQueryCount());
        }
    }
}
```

6) Funtional Test With Redirect
-------------------------------

src/Workshop/Bundle/BackendBundle/Tests/Controller/AbstractControllerTest.php

```php
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
```

src/Workshop/Bundle/BackendBundle/Tests/Controller/DefaultControllerTest.php

```php
<?php
namespace Workshop\Bundle\BackendBundle\Tests\Controller;

class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndex()
    {
        $this->requireLogin('/admin/');
    }
}
```

```
phpunit -c app src/Workshop/Bundle/BackendBundle/Tests/Controller/DefaultControllerTest
```

由於後台我們的 security 的限制，沒有登入的 User 都會被導向登入頁面。

因此我們寫一個測試看看是否真的被導向登入頁面了。

7) Funtional Test With Session
-------------------------------

可是我們要怎麼存取 Session? 例如模擬一個登入的狀態

src/Workshop/Bundle/BackendBundle/Tests/Controller/PostControllerTest.php

```php
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
```

8) Testing Form
---------------

假如我們要測試某個 Form 以及 Validator?

src/Workshop/Bundle/FrontendBundle/Tests/Form/CommentTypeTest.php

```php
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
```

這邊我們使用到了 @dataProvider 這個 annotation，這時候這時候 PHPUnit，會去呼叫 getTestFormData 這個 Method，
並且將回傳的 Array 帶入變成 $data 這個參數。

要注意的一點，由於 Symfony 的 Form 預設是會帶入 csrf token，因此這邊要記得把 csrf 關掉不然會驗證失敗。