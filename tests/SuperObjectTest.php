<?php

namespace MASNathan\Test;

use MASNathan\SuperObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \MASNathan\SuperObject
 */
class SuperObjectTest extends TestCase
{
    protected $mockArray;

    protected $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = new \StdClass();
        $this->mockObject->name = 'Test';
        $this->mockObject->last_name = 'Dummy';
        $this->mockObject->role = 'tester';
        $this->mockObject->active = true;
        $this->mockObject->tired = false;
        //Test numeric indexes
        $this->mockObject->numeric = ['1st', '2nd', '3rd'];
        // Test Childs
        $this->mockObject->tests = new \StdClass();
        $this->mockObject->tests->test_p1 = 1;
        $this->mockObject->tests->test_p2 = 2;
        $this->mockObject->tests->test_p3 = 3;
        $this->mockObject->tests->test_p4 = 4;

        $this->mockArray = (array)$this->mockObject;
        $this->mockArray['tests'] = (array)$this->mockObject->tests;
    }

    /**
     * @covers ::__construct
     */
    public function testInitEmpty()
    {
        $obj = new SuperObject();
        $this->assertEquals([], $obj->toArray());
        $this->assertEquals(new \StdClass, $obj->toObject());

        return $obj;
    }

    /**
     * @covers ::__construct
     */
    public function testInitArray()
    {
        $obj = new SuperObject($this->mockArray);
        $this->assertEquals($this->mockArray, $obj->toArray());

        return $obj;
    }

    /**
     * @covers ::__construct
     */
    public function testInitObject()
    {
        $obj = new SuperObject($this->mockObject);
        $this->assertEquals($this->mockObject, $obj->toObject());

        return $obj;
    }

    /**
     * @covers ::__get
     * @depends testInitObject
     */
    public function testMagicGet(SuperObject $obj)
    {
        $this->assertEquals($obj->name, $this->mockObject->name);
        $this->assertEquals($obj->last_name, $this->mockObject->last_name);
        $this->assertEquals($obj->tests->test_p1, $this->mockObject->tests->test_p1);
        $this->assertEquals($obj->tests->test_p2, $this->mockObject->tests->test_p2);
        $this->assertEquals($obj->tests->test_p3, $this->mockObject->tests->test_p3);
        $this->assertEquals($obj->tests->test_p4, $this->mockObject->tests->test_p4);
    }

    /**
     * @covers ::get
     * @depends testInitObject
     */
    public function testGet(SuperObject $obj)
    {
        $this->assertNull($obj->get('null_result'));
        $this->assertEquals($obj->get('name'), $this->mockObject->name);
        $this->assertEquals($obj->get('last_name'), $this->mockObject->last_name);
        $this->assertEquals($obj->get('tests')->get('test_p1'), $this->mockObject->tests->test_p1);
        $this->assertEquals($obj->get('tests')->get('test_p2'), $this->mockObject->tests->test_p2);
        $this->assertEquals($obj->get('tests')->get('test_p3'), $this->mockObject->tests->test_p3);
        $this->assertEquals($obj->get('tests')->get('test_p4'), $this->mockObject->tests->test_p4);
    }

    public function setProvider()
    {
        $obj = new SuperObject();

        return [
            [$obj, 'alias', 'value'],
            [$obj, 'number', 1],
            [$obj, 'array_data', [1, 2, 3]],
            [$obj, 'array_assoc', ['a' => 1, 'b' => 2, 'c' => 3]],
            [$obj, 'mock_array', $this->mockArray],
            [$obj, 'mock_object', $this->mockObject],
        ];
    }

    /**
     * @covers ::__set
     * @dataProvider setProvider
     */
    public function testMagicSet(SuperObject $obj, $alias, $value)
    {
        $obj->$alias = $value;
        if (is_object($obj->$alias) && get_class($obj->$alias) == 'MASNathan\\SuperObject') {
            if (is_array($value)) {
                $this->assertEquals($obj->$alias->toArray(), $value);
            } elseif (is_object($value)) {
                $this->assertEquals($obj->$alias->toObject(), $value);
            }
        } else {
            $this->assertEquals($obj->$alias, $value);
        }
    }

    /**
     * @covers ::set
     * @dataProvider setProvider
     */
    public function testSet(SuperObject $obj, $alias, $value)
    {
        $obj->set($alias, $value);
        if (is_object($obj->$alias) && get_class($obj->$alias) == 'MASNathan\\SuperObject') {
            if (is_array($value)) {
                $this->assertEquals($obj->get($alias)->toArray(), $value);
            } elseif (is_object($value)) {
                $this->assertEquals($obj->get($alias)->toObject(), $value);
            }
        } else {
            $this->assertEquals($obj->get($alias), $value);
        }
    }

    /**
     * @covers ::__call
     * @depends testInitObject
     */
    public function testObjectAccessGet(SuperObject $obj)
    {
        $this->assertEquals($obj->getName(), $this->mockObject->name);
        $this->assertEquals($obj->getLastName(), $this->mockObject->last_name);
        $this->assertInstanceOf('MASNathan\SuperObject', $obj->getTests());
        $this->assertEquals($obj->getTests()->getTestP1(), $this->mockObject->tests->test_p1);
        $this->assertEquals($obj->getTests()->getTestP2(), $this->mockObject->tests->test_p2);
        $this->assertEquals($obj->getTests()->getTestP3(), $this->mockObject->tests->test_p3);
        $this->assertEquals($obj->getTests()->getTestP4(), $this->mockObject->tests->test_p4);
        $obj = new SuperObject();
        $obj->super_test = 'This should be ok.';
        $this->assertEquals($obj->getSuperTest(), 'This should be ok.');
    }

    /**
     * @covers ::__call
     */
    public function testObjectAccessSet()
    {
        $obj = new SuperObject();
        $obj->setName('Maria');
        $this->assertEquals($obj->getName(), 'Maria');
        $obj->setLastName('Amélia');
        $this->assertEquals($obj->getLastName(), 'Amélia');
        $obj->setDetails([
            'age'        => 22,
            'profession' => 'Developer',
            'web_site'   => 'http://masnathan.com',
        ]);
        $this->assertInstanceOf('MASNathan\SuperObject', $obj->getDetails());
        $this->assertEquals($obj->getDetails()->getAge(), 22);
        $obj->getDetails()->setAge(23);
        $this->assertEquals($obj->getDetails()->getAge(), 23);
        $this->assertEquals($obj->getDetails()->getProfession(), 'Developer');
        $obj->getDetails()->setProfession('Ruler of the world');
        $this->assertEquals($obj->getDetails()->getProfession(), 'Ruler of the world');
        $this->assertEquals($obj->getDetails()->getWebSite(), 'http://masnathan.com');
        $obj->getDetails()->setWebSite('https://github.com/masnathan/object');
        $this->assertEquals($obj->getDetails()->getWebSite(), 'https://github.com/masnathan/object');
    }

    /**
     * @covers ::__call
     * @depends testInitObject
     */
    public function testObjectAccessUnset(SuperObject $obj)
    {
        $obj->property = 'value';
        $obj->super_property = 'SUPER value';
        $obj->setVisible(false);
        $obj->setTested(true);

        $this->assertEquals($obj->getProperty(), 'value');
        $this->assertEquals($obj->getSuperProperty(), 'SUPER value');
        $this->assertEquals($obj->getVisible(), false);
        $this->assertEquals($obj->getTested(), true);

        $obj->unsetProperty();
        $obj->unsetSuperProperty();
        $obj->unsetVisible();
        $obj->unsetTested();

        $this->assertEquals($obj->toObject(), $this->mockObject);
        $this->assertEquals($obj->toArray(), $this->mockArray);
    }

    /**
     * @covers ::__call
     * @depends testInitObject
     */
    public function testObjectAccessIs(SuperObject $obj)
    {
        $this->mockObject->role = 'tester';
        $this->mockObject->active = true;
        $this->mockObject->tired = false;

        $this->assertTrue($obj->isName('Test'));
        $this->assertTrue($obj->isName());
        $this->assertFalse($obj->isName('Maria'));
        $this->assertTrue($obj->isRole('tester'));
        $this->assertTrue($obj->isRole());
        $this->assertFalse($obj->isRole('developer'));
        $this->assertTrue($obj->isActive());
        $this->assertFalse($obj->isActive('boom'));
        $this->assertFalse($obj->isTired());
    }

    /**
     * @covers ::__call
     * @depends testInitObject
     */
    public function testObjectAccessCallable()
    {
        $obj = new SuperObject();
        $obj->foo = function ($a, $b) {
            return $a + $b;
        };
        $obj->bar = function ($a, $b) {
            return $a - $b;
        };

        $this->assertEquals($obj->foo(6, 4), 10);
        $this->assertEquals($obj->bar(6, 4), 2);
        $this->assertEquals($obj->doNothing(), null);
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
    public function testUnserialize($serializedData, SuperObject $obj)
    {
        $unserializedObject = unserialize($serializedData);
        $this->assertEquals($unserializedObject, $obj);

        return $unserializedObject;
    }

    /**
     * @depends testInitObject
     */
    public function testJsonSerialize(SuperObject $obj)
    {
        $jsonSerializedObject = json_encode($obj);
        $this->assertNotNull($jsonSerializedObject);
        $jsonUnserializedObjectArray = json_decode($jsonSerializedObject, true);
        $this->assertEquals($this->mockArray, $jsonUnserializedObjectArray);
    }

    /**
     * @depends testInitObject
     */
    public function testGetIterator(SuperObject $obj)
    {
        $c = 1;
        foreach ($obj->getTests() as $key => $value) {
            $this->assertEquals($c++, $value);
        }
    }

    /**
     * @depends testInitArray
     */
    public function testCount(SuperObject $obj)
    {
        $this->assertEquals(count($this->mockArray), $obj->count());
        $this->assertEquals(count($this->mockArray), count($obj));
        $this->assertEquals(count($this->mockArray['tests']), $obj->getTests()->count());
        $this->assertEquals(count($this->mockArray['tests']), count($obj->getTests()));
    }

    /**
     * @depends testInitObject
     */
    public function testToObject(SuperObject $obj)
    {
        $this->assertEquals($this->mockObject, $obj->toObject());
        $tempObject = clone $this->mockObject;
        $tempObject->tests = new SuperObject($this->mockObject->tests);
        $this->assertEquals($tempObject, $obj->toObject(false));
    }

    /**
     * @depends testInitArray
     */
    public function testToArray(SuperObject $obj)
    {
        $this->assertEquals($this->mockArray, $obj->toArray());
        $tempArray = $this->mockArray;
        $tempArray['tests'] = new SuperObject($this->mockArray['tests']);
        $this->assertEquals($tempArray, $obj->toArray(false));
    }

    /**
     * @covers ::offsetSet
     */
    public function testOffsetSet()
    {
        $obj = new SuperObject();
        $obj[] = 'Primeiro';
        $obj[2] = 'Segundo';
        $obj['3'] = 'Terceiro';
        $obj['four'] = 'Quarto';

        $this->assertEquals($obj->get(0), 'Primeiro');
        $this->assertEquals($obj->get(2), 'Segundo');
        $this->assertEquals($obj->get(3), 'Terceiro');
        $this->assertEquals($obj->getFour(), 'Quarto');

        return $obj;
    }

    /**
     * @covers ::offsetExists
     * @depends testOffsetSet
     */
    public function testOffsetExists(SuperObject $obj)
    {
        $this->assertTrue(isset($obj[0]));
        $this->assertTrue(isset($obj[2]));
        $this->assertTrue(isset($obj[3]));
        $this->assertTrue(isset($obj['four']));
        $this->assertFalse(isset($obj['bananas']));
        $this->assertFalse(isset($obj['potatos']));
        $this->assertFalse(isset($obj['parent']['child']));

        return $obj;
    }

    /**
     * @covers ::offsetGet
     * @depends testOffsetSet
     */
    public function testOffsetGet(SuperObject $obj)
    {
        $this->assertEquals($obj[0], 'Primeiro');
        $this->assertEquals($obj[2], 'Segundo');
        $this->assertEquals($obj[3], 'Terceiro');
        $this->assertEquals($obj['four'], 'Quarto');
        $this->assertNull($obj['bananas']);
        $this->assertNull($obj['potatos']);
        $this->assertNull($obj['parent']['child']);

        return $obj;
    }

    /**
     * @covers ::offsetUnset
     * @depends testOffsetExists
     */
    public function testOffsetUnset(SuperObject $obj)
    {
        unset($obj[0]);
        unset($obj[2]);
        unset($obj[3]);
        unset($obj['four']);

        $this->assertFalse(isset($obj[0]));
        $this->assertFalse(isset($obj[2]));
        $this->assertFalse(isset($obj[3]));
        $this->assertFalse(isset($obj['four']));
    }
}
