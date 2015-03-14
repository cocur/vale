<?php

namespace Cocur\Getter;

require_once __DIR__.'/MockClass.php';

use stdClass;

/**
 * HasserTest
 *
 * @package   Cocur\Getter
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class HasserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasReturnsTrueForScalarValues()
    {
        $this->assertTrue(Hasser::has('foo', []));
        $this->assertTrue(Hasser::has(42, []));
        $this->assertTrue(Hasser::has('foo', ['key']));
        $this->assertTrue(Hasser::has('foo', ['key'], 'bar'));
    }

    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasReturnsTrueIfValueInArray()
    {
        $this->assertTrue(Hasser::has(['foo', 'bar'], [0]));
        $this->assertTrue(Hasser::has(['foo'=>['bar'=>42], 'bar'], ['foo', 'bar']));
    }

    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasTakesKeyAsStringIfOnlyOneKey()
    {
        $this->assertTrue(Hasser::has(['bar' => 'foo'], 'bar'));
    }

    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasReturnsFalseWhenKeyDoesNotExistInArray()
    {
        $this->assertFalse(Hasser::has(['foo', 'bar'], [2]));
    }

    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasReturnsTrueIfValueInObject()
    {
        $obj1 = new stdClass();
        $obj1->foo = 'bar';
        $obj2 = new stdClass();
        $obj2->qoo = $obj1;

        $this->assertTrue(Hasser::has($obj1, ['foo']));
        $this->assertTrue(Hasser::has($obj2, ['qoo', 'foo']));
    }

    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasReturnsTrueIfValueFromMethod()
    {
        $obj = new MockClass();

        $this->assertTrue(Hasser::has($obj, ['foo']));
        $this->assertTrue(Hasser::has($obj, ['bar']));
        $this->assertTrue(Hasser::has($obj, ['qoo']));
        $this->assertTrue(Hasser::has($obj, ['qoz']));
    }

    /**
     * @test
     * @covers Cocur\Getter\Hasser::has()
     */
    public function hasReturnsTrueIfValueFromMixedData()
    {
        $obj1 = new stdClass();
        $obj1->foo = 'bar';

        $this->assertTrue(Hasser::has(['qoo' => $obj1], ['qoo', 'foo']));
    }
}
