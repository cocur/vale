<?php

require_once __DIR__.'/../vendor/autoload.php';

use Cocur\Getter\Getter;

$joffrey = new Person('Joffrey');
$myrcella = new Person('Myrcella');
$tommen = new Person('Tommen');
$cersei = new Person('Cersei', [$joffrey, $myrcella, $tommen]);

$firstChildName = Getter::get($cersei, ['children', 0, 'firstName']);
print_r($cersei);
echo "Name of first child: ".$firstChildName."\n";

class Person
{
    public $children = [];
    private $firstName;

    public function __construct($firstName, $children = [])
    {
        $this->firstName = $firstName;
        $this->children  = $children;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }
}
