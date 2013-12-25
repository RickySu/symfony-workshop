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