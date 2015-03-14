<?php

namespace Cocur\Getter;
use stdClass;
use Mockery;

/**
 * GetterTest
 *
 * @package   Cocur\Getter
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class GetterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\Getter\Getter::get()
     */
    public function getReturnsScalarValueAsIs()
    {
        $this->assertSame('foo', Getter::get('foo', []));
        $this->assertSame(42, Getter::get(42, []));
        $this->assertSame('foo', Getter::get('foo', ['key']));
        $this->assertSame('foo', Getter::get('foo', ['key'], 'bar'));
    }

    /**
     * @test
     * @covers Cocur\Getter\Getter::get()
     */
    public function getReturnsValueFromArray()
    {
        $this->assertSame('foo', Getter::get(['foo', 'bar'], [0]));
        $this->assertSame(42, Getter::get(['foo'=>['bar'=>42], 'bar'], ['foo', 'bar']));
    }

    /**
     * @test
     * @covers Cocur\Getter\Getter::get()
     */
    public function getReturnsDefaultWhenKeyDoesNotExistInArray()
    {
        $this->assertNull(Getter::get(['foo', 'bar'], [2]));
    }

    /**
     * @test
     * @covers Cocur\Getter\Getter::get()
     */
    public function getReturnsValueFromObject()
    {
        $obj1 = new stdClass();
        $obj1->foo = 'bar';
        $obj2 = new stdClass();
        $obj2->qoo = $obj1;

        $this->assertSame('bar', Getter::get($obj1, ['foo']));
        $this->assertSame('bar', Getter::get($obj2, ['qoo', 'foo']));
    }

    /**
     * @test
     * @covers Cocur\Getter\Getter::get()
     */
    public function getReturnsValueFromMethod()
    {
        $obj = new MockClass();

        $this->assertSame('foo', Getter::get($obj, ['foo']));
        $this->assertSame('bar', Getter::get($obj, ['bar']));
        $this->assertSame('qoo', Getter::get($obj, ['qoo']));
        $this->assertSame('qoz', Getter::get($obj, ['qoz']));
    }

    /**
     * @test
     * @covers Cocur\Getter\Getter::get()
     */
    public function getReturnsValueFromMixedData()
    {
        $obj1 = new stdClass();
        $obj1->foo = 'bar';

        $this->assertSame('bar', Getter::get(['qoo' => $obj1], ['qoo', 'foo']));
    }
}

class MockClass {
    public function getFoo() { return 'foo'; }
    public function isBar() { return 'bar'; }
    public function hasQoo() { return 'qoo'; }
    public function qoz() { return 'qoz'; }
}
