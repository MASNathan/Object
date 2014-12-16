<?php

use MASNathan\Object;

class ObjectTest extends PHPUnit_Framework_TestCase
{

    public function testStuff()
    {
        $obj = new Object(['test_value' => 123]);
        $this->assertEquals($obj->getTestValue(), 123);
    }
}
