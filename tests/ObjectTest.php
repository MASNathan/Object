<?php

use MASNathan\Object;

class ObjectTest extends PHPUnit_Framework_TestCase
{

    protected $mockArray = [
        'name' => 'Test',
        'last_name' => 'Dummy',
        'tests' => [
            'test_p1' => 1,
            'test_p2' => 2,
            'test_p3' => 3,
            'test_p4' => 4,
        ]
    ];

    public function testInitEmpty()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $obj = new Object();
        $this->assertEquals((array) $obj, []);
        
        return $obj;
    }

    public function testInitArray()
    {
        $obj = new Object($this->mockArray);
        //$this->assertEquals($x, $obj->toArray());

        return $obj;
    }

    public function testInitObject()
    {
        $x = new stdClass();
        $x->name = 'Test';
        $x->last_name = 'Dummy';
        $x->tests = new stdClass();
        $x->tests->test_p1 = 1;
        $x->tests->test_p2 = 2;
        $x->tests->test_p3 = 3;
        $x->tests->test_p4 = 4;

        $obj = new Object($x);
        //$this->assertNotEmpty((array) $obj);

        return $obj;
    }

    /**
     * @depends testInitArray
     * @depends testInitObject
     */
    public function testObjectAccessGet(Object $array, Object $obj)
    {
        $this->assertEquals($array->getName(), 'Test');
        $this->assertEquals($array->getLastName(), 'Dummy');
        $this->assertInstanceOf('MASNathan\Object', $array->getTests());
        $this->assertEquals($array->getTests()->getTestP1(), 1);
        $this->assertEquals($array->getTests()->getTestP2(), 2);
        $this->assertEquals($array->getTests()->getTestP3(), 3);
        $this->assertEquals($array->getTests()->getTestP4(), 4);

        $this->assertEquals($obj->getName(), 'Test');
        $this->assertEquals($obj->getLastName(), 'Dummy');
        $this->assertInstanceOf('MASNathan\Object', $obj->getTests());
        $this->assertEquals($obj->getTests()->getTestP1(), 1);
        $this->assertEquals($obj->getTests()->getTestP2(), 2);
        $this->assertEquals($obj->getTests()->getTestP3(), 3);
        $this->assertEquals($obj->getTests()->getTestP4(), 4);
    }

    /**
     * @depends testInitObject
     */
    public function testSerialize($obj)
    {
        $serializedData = serialize($obj);

        $this->assertNotNull($serializedData);
        return $serializedData;
    }

    /**
     * @depends testSerialize
     * @depends testInitObject
     */
    public function testUnserialize($serializedData, $obj)
    {
        $unserializedObject = unserialize($serializedData);
        $this->assertEquals($unserializedObject, $obj);

        return $unserializedObject;
    }

    /**
     * @depends testInitObject
     */
    public function testJsonSerialize($obj)
    {
        $jsonSerializedObject = json_encode($obj);
        $this->assertNotNull($jsonSerializedObject);
        $jsonUnserializedObjectArray = json_decode($jsonSerializedObject, true);
        $this->assertEquals($this->mockArray, $jsonUnserializedObjectArray);
    }
}
