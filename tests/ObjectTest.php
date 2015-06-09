<?php

namespace MASNathan\Test;

use MASNathan\Object;

/**
 * @coversDefaultClass \MASNathan\Object
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{

    protected $mockArray;
    protected $mockObject;

    protected function setUp()
    {
        $this->mockArray = array();
        $this->mockArray['name'] = 'Test';
        $this->mockArray['last_name'] = 'Dummy';
        //Test numeric indexes
        $this->mockArray['numeric'] = array('1st', '2nd', '3rd');
        // Test Childs
        $this->mockArray['tests'] = array();
        $this->mockArray['tests']['test_p1'] = 1;
        $this->mockArray['tests']['test_p2'] = 2;
        $this->mockArray['tests']['test_p3'] = 3;
        $this->mockArray['tests']['test_p4'] = 4;

        $this->mockObject = new \stdClass();
        $this->mockObject->name = 'Test';
        $this->mockObject->last_name = 'Dummy';
        //Test numeric indexes
        $this->mockObject->numeric = array('1st', '2nd', '3rd');
        // Test Childs
        $this->mockObject->tests = new \stdClass();
        $this->mockObject->tests->test_p1 = 1;
        $this->mockObject->tests->test_p2 = 2;
        $this->mockObject->tests->test_p3 = 3;
        $this->mockObject->tests->test_p4 = 4;
    }

    /**
     * @covers ::__construct
     */
    public function testInitEmpty()
    {
        $obj = new Object();
        $this->assertEquals(array(), $obj->toArray());
        $this->assertEquals(new \stdClass, $obj->toObject());
        
        return $obj;
    }

    /**
     * @covers ::__construct
     */
    public function testInitArray()
    {
        $obj = new Object($this->mockArray);
        $this->assertEquals($this->mockArray, $obj->toArray());
        return $obj;
    }

    /**
     * @covers ::__construct
     */
    public function testInitObject()
    {
        $obj = new Object($this->mockObject);
        $this->assertEquals($this->mockObject, $obj->toObject());
        return $obj;
    }

    /**
     * @covers ::__get
     * @depends testInitObject
     */
    public function testMagicGet(Object $obj)
    {
        $this->assertEquals($obj->name, $this->mockObject->name);
        $this->assertEquals($obj->last_name, $this->mockObject->last_name);
        $this->assertEquals($obj->tests->test_p1, $this->mockObject->tests->test_p1);
        $this->assertEquals($obj->tests->test_p2, $this->mockObject->tests->test_p2);
        $this->assertEquals($obj->tests->test_p3, $this->mockObject->tests->test_p3);
        $this->assertEquals($obj->tests->test_p4, $this->mockObject->tests->test_p4);
    }

    /**
     * @covers ::__call
     * @depends testInitObject
     */
    public function testObjectAccessGet(Object $obj)
    {
        $this->assertEquals($obj->getName(), $this->mockObject->name);
        $this->assertEquals($obj->getLastName(), $this->mockObject->last_name);
        $this->assertInstanceOf('MASNathan\Object', $obj->getTests());
        $this->assertEquals($obj->getTests()->getTestP1(), $this->mockObject->tests->test_p1);
        $this->assertEquals($obj->getTests()->getTestP2(), $this->mockObject->tests->test_p2);
        $this->assertEquals($obj->getTests()->getTestP3(), $this->mockObject->tests->test_p3);
        $this->assertEquals($obj->getTests()->getTestP4(), $this->mockObject->tests->test_p4);
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

    /**
     * @depends testInitObject
     */
    public function testGetIterator($obj)
    {
        $c = 1;
        foreach ($obj->getTests() as $key => $value) {
            $this->assertEquals($c++, $value);
        }
    }
}
