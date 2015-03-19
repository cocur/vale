<?php


namespace Cocur\Vale;

use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * AccessorTest
 *
 * @package   Cocur\Vale
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @group     unit
 */
class AccessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\Vale\Accessor::__construct()
     * @covers Cocur\Vale\Accessor::getData()
     */
    public function getDataReturnsData()
    {
        $accessor = new Accessor(['foo' => 'bar']);

        $this->assertSame('bar', $accessor->getData()['foo']);
    }
    /**
     * @test
     * @covers Cocur\Vale\Accessor::__construct()
     * @covers Cocur\Vale\Accessor::getData()
     */
    public function getDataReturnsObjectData()
    {
        $obj = new stdClass();
        $obj->foo = 'bar';
        $accessor = new Accessor($obj);

        $this->assertSame('bar', $accessor->getData()->foo);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::__construct()
     * @covers Cocur\Vale\Accessor::getCurrent()
     */
    public function getCurrentReturnsDataAfterConstruction()
    {
        $accessor = new Accessor(['foo' => 'bar']);

        $this->assertSame('bar', $accessor->getCurrent()['foo']);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     */
    public function toGoesToKeyInArray()
    {
        $accessor = new Accessor(['level1' => 'bar']);

        $this->assertTrue($accessor->to('level1'));
        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     */
    public function toGoesToKeyInNestedArray()
    {
        $accessor = new Accessor(['level1' => ['level2' => 'bar']]);
        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->to('level2'));

        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     */
    public function toReturnsFalseIfKeyDoesNotExistInArray()
    {
        $accessor = new Accessor([]);

        $this->assertFalse($accessor->to('invalid'));
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInObject()
    {
        $obj = new stdClass();
        $obj->level1 = 'bar';
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInNestedObject()
    {
        $obj = new stdClass();
        $obj->level1 = new stdClass();
        $obj->level1->level2 = 'bar';
        $accessor = new Accessor($obj);
        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->to('level2'));

        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toReturnsFalseIfKeyDoesNotExistInObject()
    {
        $accessor = new Accessor(new stdClass());

        $this->assertFalse($accessor->to('invalid'));
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInObjectUsingMethod()
    {
        eval('class AccessorTestMockToGoesToKeyInObjectUsingMethod { public function level1() { return "bar"; } }');
        $obj = new \AccessorTestMockToGoesToKeyInObjectUsingMethod();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInNestedObjectUsingMethod()
    {
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingMethod2 { public function level2() { return "bar"; } }');
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingMethod { public function level1() { return new AccessorTestMockToGoesToKeyInNestedObjectUsingMethod2(); } }');
        $obj = new \AccessorTestMockToGoesToKeyInNestedObjectUsingMethod();
        $accessor = new Accessor($obj);
        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->to('level2'));

        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toReturnsFalseIfKeyDoesNotExistInObjectUsingMethod()
    {
        eval('class AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingMethod {}');
        $obj = new \AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingMethod();
        $accessor = new Accessor($obj);

        $this->assertFalse($accessor->to('invalid'));
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInObjectUsingGetter()
    {
        eval('class AccessorTestMockToGoesToKeyInObjectUsingGetter { public function getLevel1() { return "bar"; } }');
        $obj = new \AccessorTestMockToGoesToKeyInObjectUsingGetter();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInNestedObjectUsingGetter()
    {
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingGetter2 { public function getLevel2() { return "bar"; } }');
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingGetter { public function getLevel1() { return new AccessorTestMockToGoesToKeyInNestedObjectUsingGetter2(); } }');
        $obj = new \AccessorTestMockToGoesToKeyInNestedObjectUsingGetter();
        $accessor = new Accessor($obj);
        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->to('level2'));

        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toReturnsFalseIfKeyDoesNotExistInObjectUsingGetter()
    {
        eval('class AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingGetter {}');
        $obj = new \AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingGetter();
        $accessor = new Accessor($obj);

        $this->assertFalse($accessor->to('invalid'));
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInObjectUsingHasser()
    {
        eval('class AccessorTestMockToGoesToKeyInObjectUsingHasser { public function hasLevel1() { return "bar"; } }');
        $obj = new \AccessorTestMockToGoesToKeyInObjectUsingHasser();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInNestedObjectUsingHasser()
    {
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingHasser2 { public function hasLevel2() { return "bar"; } }');
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingHasser { public function hasLevel1() { return new AccessorTestMockToGoesToKeyInNestedObjectUsingHasser2(); } }');
        $obj = new \AccessorTestMockToGoesToKeyInNestedObjectUsingHasser();
        $accessor = new Accessor($obj);
        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->to('level2'));

        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toReturnsFalseIfKeyDoesNotExistInObjectUsingHasser()
    {
        eval('class AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingHasser {}');
        $obj = new \AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingHasser();
        $accessor = new Accessor($obj);

        $this->assertFalse($accessor->to('invalid'));
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInObjectUsingIsser()
    {
        eval('class AccessorTestMockToGoesToKeyInObjectUsingIsser { public function isLevel1() { return "bar"; } }');
        $obj = new \AccessorTestMockToGoesToKeyInObjectUsingIsser();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toGoesToKeyInNestedObjectUsingIsser()
    {
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingIsser2 { public function isLevel2() { return "bar"; } }');
        eval('class AccessorTestMockToGoesToKeyInNestedObjectUsingIsser { public function isLevel1() { return new AccessorTestMockToGoesToKeyInNestedObjectUsingIsser2(); } }');
        $obj = new \AccessorTestMockToGoesToKeyInNestedObjectUsingIsser();
        $accessor = new Accessor($obj);
        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->to('level2'));

        $this->assertSame('bar', $accessor->getCurrent());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::to()
     * @covers Cocur\Vale\Accessor::isObjectWithMethod()
     */
    public function toReturnsFalseIfKeyDoesNotExistInObjectUsingIsser()
    {
        eval('class AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingIsser {}');
        $obj = new \AccessorTestMockToReturnsFalseIfKeyDoesNotExistInObjectUsingIsser();
        $accessor = new Accessor($obj);

        $this->assertFalse($accessor->to('invalid'));
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsExistingValueInArray()
    {
        $accessor = new Accessor(['level1' => 'bar']);

        $this->assertTrue($accessor->set('level1', 'foo'));
        $this->assertSame('foo', $accessor->getData()['level1']);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsExistingValueInNestedArray()
    {
        $accessor = new Accessor(['level1' => ['level2' => 'bar']]);

        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->set('level2', 'foo'));
        $this->assertSame('foo', $accessor->getData()['level1']['level2']);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsNewValueInArray()
    {
        $accessor = new Accessor(['level1a' => 'bar']);

        $this->assertTrue($accessor->set('level1b', 'foo'));
        $this->assertSame('bar', $accessor->getData()['level1a']);
        $this->assertSame('foo', $accessor->getData()['level1b']);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsNewValueInNestedArray()
    {
        $accessor = new Accessor(['level1' => ['level2a' => 'bar']]);

        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->set('level2b', 'foo'));
        $this->assertSame('bar', $accessor->getData()['level1']['level2a']);
        $this->assertSame('foo', $accessor->getData()['level1']['level2b']);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsExistingValueInObject()
    {
        $obj = new stdClass();
        $obj->level1 = 'bar';
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->set('level1', 'foo'));
        $this->assertSame('foo', $accessor->getData()->level1);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsExistingValueInNestedObject()
    {
        $obj = new stdClass();
        $obj->level1 = new stdClass();
        $obj->level1->level2 = 'bar';
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->set('level2', 'foo'));
        $this->assertSame('foo', $accessor->getData()->level1->level2);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsNewValueInObject()
    {
        $obj = new stdClass();
        $obj->level1a = 'bar';
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->set('level1b', 'foo'));
        $this->assertSame('bar', $accessor->getData()->level1a);
        $this->assertSame('foo', $accessor->getData()->level1b);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsNewValueInNestedObject()
    {
        $obj = new stdClass();
        $obj->level1 = new stdClass();
        $obj->level1->level2a = 'bar';
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->set('level2b', 'foo'));
        $this->assertSame('bar', $accessor->getData()->level1->level2a);
        $this->assertSame('foo', $accessor->getData()->level1->level2b);
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsValueInObjectUsingMethod()
    {
        eval('class AccessorTestMockSetSetsValueInObjectUsingMethod { protected $v = "bar"; public function level1($v = null) { if ($v) $this->v = $v; return $this->v; } }');
        $obj = new \AccessorTestMockSetSetsValueInObjectUsingMethod();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->set('level1', 'foo'));
        $this->assertSame('foo', $accessor->getData()->level1());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsValueInNestedObjectUsingMethod()
    {
        eval('class AccessorTestMockSetsValueInNestedObjectUsingMethod2 { protected $v = "bar"; public function level2($v = null) { if ($v) $this->v = $v; return $this->v; } }');
        eval('class AccessorTestMockSetsValueInNestedObjectUsingMethod { function __construct() { $this->i = new AccessorTestMockSetsValueInNestedObjectUsingMethod2(); } public function level1() { return $this->i; } }');
        $obj = new \AccessorTestMockSetsValueInNestedObjectUsingMethod();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->set('level2', 'foo'));
        $this->assertSame('foo', $accessor->getData()->level1()->level2());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsValueInObjectUsingSetter()
    {
        eval('class AccessorTestMockSetSetsValueInObjectUsingSetter { protected $v; public function setLevel1($v) { $this->v = $v; } function getLevel1() { return $this->v; } }');
        $obj = new \AccessorTestMockSetSetsValueInObjectUsingSetter();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->set('level1', 'foo'));
        $this->assertSame('foo', $accessor->getData()->getLevel1());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setSetsValueInNestedObjectUsingSetter()
    {
        eval('class AccessorTestMockSetsValueInNestedObjectUsingSetter2 { protected $v; public function setLevel2($v) { $this->v = $v; } public function getLevel2() { return $this->v; } }');
        eval('class AccessorTestMockSetsValueInNestedObjectUsingSetter { function __construct() { $this->i = new AccessorTestMockSetsValueInNestedObjectUsingSetter2(); } public function level1() { return $this->i; } }');
        $obj = new \AccessorTestMockSetsValueInNestedObjectUsingSetter();
        $accessor = new Accessor($obj);

        $this->assertTrue($accessor->to('level1'));
        $this->assertTrue($accessor->set('level2', 'foo'));
        $this->assertSame('foo', $accessor->getData()->level1()->getLevel2());
    }

    /**
     * @test
     * @covers Cocur\Vale\Accessor::set()
     */
    public function setReturnsFalseIfValueCouldNotBeSet()
    {
        $accessor = new Accessor('invalid');

        $this->assertFalse($accessor->set('level1', 'foo'));
    }
}
