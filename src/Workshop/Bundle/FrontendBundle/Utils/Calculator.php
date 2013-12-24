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
