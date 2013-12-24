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
