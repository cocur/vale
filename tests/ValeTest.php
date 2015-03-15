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
     * @covers Cocur\Vale\Vale::getValue()
     */
    public function getValueReturnsNullIfKeyDoesNotExistInObject()
    {
        $data = new stdClass();

        $this->assertNull($this->vale->getValue($data, ['name']), 'Return null if $keys does not exist in object');
        $this->assertNull(
            $this->vale->getValue($data, ['family', 'name']),
            'Return null if $keys does not exist in object'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     */
    public function getValueReturnsValueFromObjectProperty()
    {
        $data = new stdClass();
        $data->name = 'Tyrion';
        $data->family = new stdClass();
        $data->family->name = 'Lannister';

        $this->assertSame('Tyrion', $this->vale->getValue($data, ['name']), 'Return value from flat object');
        $this->assertSame(
            'Lannister',
            $this->vale->getValue($data, ['family', 'name']),
            'Return value from nested object'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsValueFromObjectMethod()
    {
        eval('class FamilyObjM { function name() { return "Lannister"; }}');
        eval('class PersonObjM { function name() { return "Tyrion"; } function family() { return new \FamilyObjM(); }}');
        $data = new \PersonObjM();

        $this->assertSame('Tyrion', $this->vale->getValue($data, ['name']), 'Return value from flat object method');
        $this->assertSame(
            'Lannister',
            $this->vale->getValue($data, ['family', 'name']),
            'Return value from nested object methods'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsNullIfObjectMethodIsNotPublic()
    {
        eval('class FamilyObjMPro { protected function name() { return "Lannister"; }}');
        eval('class PersonObjMPro { protected function family() { return new \FamilyObjMPro(); }}');
        $data = new \PersonObjMPro();

        $this->assertNull($this->vale->getValue($data, ['name']), 'Return null if method not callable');
        $this->assertNull($this->vale->getValue($data, ['family', 'name']),  'Return null if method not callable'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsValueFromObjectGetter()
    {
        eval('class FamilyObjG { function getName() { return "Lannister"; }}');
        eval('class PersonObjG { function getName() { return "Tyrion"; } function getFamily() { return new \FamilyObjG(); }}');
        $data = new \PersonObjG();

        $this->assertSame('Tyrion', $this->vale->getValue($data, ['name']), 'Return value from flat object getter');
        $this->assertSame(
            'Lannister',
            $this->vale->getValue($data, ['family', 'name']),
            'Return value from nested object getters'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsNullIfObjectGetterIsNotPublic()
    {
        eval('class FamilyObjGPro { protected function getName() { return "Lannister"; }}');
        eval('class PersonObjGPro { protected function getFamily() { return new \FamilyObjGPro(); }}');
        $data = new \PersonObjGPro();

        $this->assertNull($this->vale->getValue($data, ['name']), 'Return null if method not callable');
        $this->assertNull($this->vale->getValue($data, ['family', 'name']),  'Return null if method not callable'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsValueFromObjectHasser()
    {
        eval('class FamilyObjH { function hasName() { return true; }}');
        eval('class PersonObjH { function hasName() { return true; } function hasFamily() { return new \FamilyObjH(); }}');
        $data = new \PersonObjH();

        $this->assertTrue($this->vale->getValue($data, ['name']), 'Return value from flat object hasser');
        $this->assertTrue($this->vale->getValue($data, ['family', 'name']),  'Return value from nested object hassers');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsNullIfObjectHasserIsNotPublic()
    {
        eval('class FamilyObjHPro { protected function hasName() { return "Lannister"; }}');
        eval('class PersonObjHPro { protected function hasFamily() { return new \FamilyObjHPro(); }}');
        $data = new \PersonObjHPro();

        $this->assertNull($this->vale->getValue($data, ['name']), 'Return null if method not callable');
        $this->assertNull($this->vale->getValue($data, ['family', 'name']),  'Return null if method not callable'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsValueFromObjectIsser()
    {
        eval('class FamilyObjI { function isName() { return true; }}');
        eval('class PersonObjI { function isName() { return true; } function isFamily() { return new \FamilyObjI(); }}');
        $data = new \PersonObjI();

        $this->assertTrue($this->vale->getValue($data, ['name']), 'Return value from flat object isser');
        $this->assertTrue($this->vale->getValue($data, ['family', 'name']),  'Return value from nested object issers');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::getValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function getValueReturnsNullIfObjectIsserIsNotPublic()
    {
        eval('class FamilyObjIPro { protected function isName() { return "Lannister"; }}');
        eval('class PersonObjIPro { protected function isFamily() { return new \FamilyObjIPro(); }}');
        $data = new \PersonObjIPro();

        $this->assertNull($this->vale->getValue($data, ['name']), 'Return null if method not callable');
        $this->assertNull($this->vale->getValue($data, ['family', 'name']),  'Return null if method not callable'
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
     * @covers Cocur\Vale\Vale::setValue()
     */
    public function setValueAddsValueToArray()
    {
        $data = ['name' => 'Tyrion', 'family' => []];

        $result = $this->vale->setValue($data, ['wife'], 'Sansa Stark');
        $this->assertSame('Sansa Stark', $result['wife'], 'Adds value to flat array');

        $result = $this->vale->setValue($data, ['family', 'seat'], 'Casterly Rock');
        $this->assertSame('Casterly Rock', $result['family']['seat'], 'Adds value to nested array');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     */
    public function setValueSetsValueInObjectUsingProperty()
    {
        $data = new stdClass();
        $data->name = 'Tyrion';
        $data->family = new stdClass();
        $data->family->name = 'Lannister';

        $result = $this->vale->setValue($data, ['name'], 'Cersei');
        $this->assertSame('Cersei', $result->name, 'Sets value in flat object');

        $result = $this->vale->setValue($data, ['family', 'name'], 'Baratheon');
        $this->assertSame('Baratheon', $result->family->name, 'Sets value in nested object');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     */
    public function setValueAddsValueToObjectUsingProperty()
    {
        $data = new stdClass();
        $data->name = 'Tyrion';
        $data->family = new stdClass();

        $result = $this->vale->setValue($data, ['wife'], 'Sansa Stark');
        $this->assertSame('Sansa Stark', $result->wife, 'Adds value to flat object');

        $result = $this->vale->setValue($data, ['family', 'seat'], 'Casterly Rock');
        $this->assertSame('Casterly Rock', $result->family->seat, 'Adds value to nested object');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function setValueSetsValueInObjectUsingMethod()
    {
        eval('class PersonSetObjM { private $name; function name($name = null) { if ($name) $this->name = $name; return $this->name; }}');
        $data = new \PersonSetObjM();
        $data->name('Tyrion');

        $result = $this->vale->setValue($data, ['name'], 'Cersei');
        $this->assertSame('Cersei', $result->name(), 'Sets value in flat object using method');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function setValueSetsValueInObjectUsingSetter()
    {
        eval('class PersonObjS { private $name; function setName($name) { $this->name = $name; } function getName() { return $this->name; }}');
        $data = new \PersonObjS();
        $data->setName('Tyrion');

        $result = $this->vale->setValue($data, ['name'], 'Cersei');
        $this->assertSame('Cersei', $result->getName(), 'Sets value in flat object');
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function setValueSetsValueThroughNavigationMethod()
    {
        eval('class FamilySetObjM { private $name; function setName($name) { $this->name = $name; } function getName() { return $this->name; }}');
        eval('class PersonSetObjM2 { private $family; function __construct() { $this->family = new \FamilySetObjM(); } function family() { return $this->family; }}');
        $data = new \PersonSetObjM2();

        $result = $this->vale->setValue($data, ['family', 'name'], 'Baratheon');
        $this->assertSame(
            'Baratheon',
            $result->family()->getName(),
            'Return value from nested object getters'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function setValueSetsValueThroughNavigationGetter()
    {
        eval('class FamilySetObjG { private $name; function setName($name) { $this->name = $name; } function getName() { return $this->name; }}');
        eval('class PersonSetObjG { private $family; function __construct() { $this->family = new \FamilySetObjG(); } function getFamily() { return $this->family; }}');
        $data = new \PersonSetObjG();

        $result = $this->vale->setValue($data, ['family', 'name'], 'Baratheon');
        $this->assertSame(
            'Baratheon',
            $result->getFamily()->getName(),
            'Return value from nested object getters'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function setValueSetsValueThroughNavigationHasser()
    {
        eval('class FamilySetObjH { private $name; function setName($name) { $this->name = $name; } function getName() { return $this->name; }}');
        eval('class PersonSetObjH { private $family; function __construct() { $this->family = new \FamilySetObjH(); } function hasFamily() { return $this->family; }}');
        $data = new \PersonSetObjH();

        $result = $this->vale->setValue($data, ['family', 'name'], 'Baratheon');
        $this->assertSame(
            'Baratheon',
            $result->hasFamily()->getName(),
            'Return value from nested object getters'
        );
    }

    /**
     * @test
     * @covers Cocur\Vale\Vale::setValue()
     * @covers Cocur\Vale\Vale::isObjectWithMethod()
     */
    public function setValueSetsValueThroughNavigationIsser()
    {
        eval('class FamilySetObjI { private $name; function setName($name) { $this->name = $name; } function getName() { return $this->name; }}');
        eval('class PersonSetObjI { private $family; function __construct() { $this->family = new \FamilySetObjI(); } function isFamily() { return $this->family; }}');
        $data = new \PersonSetObjI();

        $result = $this->vale->setValue($data, ['family', 'name'], 'Baratheon');
        $this->assertSame(
            'Baratheon',
            $result->isFamily()->getName(),
            'Return value from nested object getters'
        );
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
}
