<?php

namespace Cocur\Vale;

use InvalidArgumentException;
use stdClass;

/**
 * ValeTest
 *
 * @package   Cocur\Vale
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class ValeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Vale
     */
    private $vale;

    public function setUp()
    {
        $this->vale = new Vale();
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::instance()
     */
    public function instanceReturnsInstanceOfVale()
    {
        $this->assertInstanceOf('Cocur\Vale\Vale', Vale::instance());
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::get()
     */
    public function getReturnsValue()
    {
        $this->assertSame('Tyrion', Vale::get(['name' => 'Tyrion'], ['name']));
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::set()
     */
    public function setSetsValue()
    {
        $result = Vale::set([], ['name'], 'Tyrion');

        $this->assertSame('Tyrion', $result['name']);
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::has()
     */
    public function hasReturnsIfValueExists()
    {
        $this->assertTrue(Vale::has(['name' => 'Tyrion'], ['name']));
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isKeysEmpty()
     */
    public function getValueReturnsDataIfKeysIsEmpty()
    {
        $data = ['name' => 'Tyrion'];

        $this->assertSame($data, $this->vale->getValue($data, []), 'Return $data if $keys is empty array');
        $this->assertSame($data, $this->vale->getValue($data, ''), 'Return $data is $keys is empty string');
        $this->assertSame($data, $this->vale->getValue($data, null), 'Return $data if $keys is null');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     */
    public function getValueReturnsNullIfKeyDoesNotExistInArray()
    {
        $data = [];

        $this->assertNull($this->vale->getValue($data, ['name']), 'Return null if $keys does not exist in array');
        $this->assertNull(
            $this->vale->getValue($data, ['family', 'name']),
            'Return null if $keys does not exist in array'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     */
    public function getValueReturnsValueFromArray()
    {
        $data = ['name' => 'Tyrion', 'family' => ['name' => 'Lannister']];

        $this->assertSame('Tyrion', $this->vale->getValue($data, ['name']), 'Return value from flat array');
        $this->assertSame(
            'Lannister',
            $this->vale->getValue($data, ['family', 'name']),
            'Return value from nested array'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     */
    public function setValueReturnsDataIfKeysIsEmpty()
    {
        $data = ['name' => 'Tyrion'];

        $this->assertSame($data, $this->vale->setValue($data, [], 'x'), 'Return $data if $keys is empty array');
        $this->assertSame($data, $this->vale->setValue($data, '', 'x'), 'Return $data is $keys is empty string');
        $this->assertSame($data, $this->vale->setValue($data, null, 'x'), 'Return $data if $keys is null');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     */
    public function setValueSetsValueInArray()
    {
        $data = ['name' => 'Tyrion', 'family' => ['name' => 'Lannister']];

        $result = $this->vale->setValue($data, ['name'], 'Cersei');
        $this->assertSame('Cersei', $result['name'], 'Sets value in flat array');

        $result = $this->vale->setValue($data, ['family', 'name'], 'Baratheon');
        $this->assertSame('Baratheon', $result['family']['name'], 'Sets value in nested array');
    }

    /**
     * @test
     * @covers                   Cocur\Vale\Vale::setValue()
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Did not find path ["family","seat"] in structure []
     */
    public function setValueThrowsExceptionIfKeyInPathDoesNotExist()
    {
        $this->vale->setValue([], ['family', 'seat'], 'Casterly Rock');
    }

    /**
     * @test
     * @covers                   Cocur\Vale\Vale::setValue()
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Did not set path ["family","seat"] in structure {"family":"Lannister"}
     */
    public function setValueThrowsExceptionIfValueCanNotBeSetInPath()
    {
        // This should really only happen if an element in the middle of the path is a scalar value.
        $this->vale->setValue(['family' => 'Lannister'], ['family', 'seat'], 'Casterly Rock');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::hasValue()
     */
    public function hasValueReturnsTrueIfKeysIsEmpty()
    {
        $this->assertTrue($this->vale->hasValue(['foo' => 'bar'], ''));
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::hasValue()
     */
    public function hasValueReturnsTrueIfValueExists()
    {
        $this->assertTrue($this->vale->hasValue(['foo' => 'bar'], ['foo']));
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::hasValue()
     */
    public function hasValueReturnsFalseIfValueNotExists()
    {
        $this->assertFalse($this->vale->hasValue(['foo' => 'bar'], ['invalid']));
    }
}
