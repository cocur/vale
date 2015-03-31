Vale
====

> Vale helps you working with complex data structures. Easily get, set, unset and check the existence of values in
  deeply nested arrays and objects.

[![Build Status](https://img.shields.io/travis/cocur/vale/master.svg?style=flat)](https://travis-ci.org/cocur/vale)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/cocur/vale.svg?style=flat)](https://scrutinizer-ci.com/g/cocur/vale/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/cocur/vale.svg?style=flat)](https://scrutinizer-ci.com/g/cocur/vale/?branch=master)

Developed by [Florian Eckerstorfer](https://florian.ec) in Vienna, Europe.


Features
--------

- Get, set, unset and check the existence of values in deeply nested arrays and objects
- Works with arbitrary arrays and objects and any combination of them
- Uses getters, setters, unsetters, hassers and issers in objects

```php
$name = Vale::get($families, ['lannister', 'leader', 'children', 2, 'name']);

// This would be equal to the following
$name = null;
if (isset($families['lannister']) && $families['lannister']) {
    if ($families['lannister']->getLeader()) {
        if (isset($families['lannister']->getLeader()->children[2]) && $families['lannister']->getLeader()->children[2]) {
            $name = $families['lannister']->getLeader()->children[2]->name();
        }
    }
}
```


Installation
------------

You can install Vale using [Composer](https://getcomposer.org):

```shell
$ composer require cocur/vale
```


Usage
-----

You can use either the static methods provided by Vale or create an instance of Vale.

```php
use Cocur\Vale\Vale;

$data = ['name' => 'Tyrion'];
Vale::get($data, ['name']); // -> "Tyrion"
Vale::set($data, ['name'], 'Cersei'); // -> ["name" => "Cersei"]
Vale::has($data, ['name']); // -> true
Vale::remove($data, ['name']); // -> []

$vale = new Vale();
$vale->getValue($data, ['name']); // -> "Tyrion"
$vale->setValue($data, ['name'], 'Cersei'); // -> ["name" => "Cersei"]
$vale->hasValue($data, ['name']); // -> true
$vale->removeValue($data, ['name']); // -> []
```

For flat arrays and objects (that is, arrays and objects with only one level of depth) you can also use a string
or integer as key. This works for the static as well as the instance methods.

```php
Vale::get(['name' => 'Tyrion'], 'name'); // -> "Tyrion"
Vale::get(['Tyrion'], 0); // -> "Tyrion"
```


### Get

`::get()` and `->getValue()` return the value of a specified element.

```php
mixed get(mixed $data, array|string|int $keys, mixed $default = null)
mixed getValue(mixed $data, array|string|int $keys, mixed $default = null)
```

- `$data` is an arbitrary data structure
- `$keys` is an array of keys to access the value. If the length is `1`, `$keys` can be a string or int
- `$default` is the default value that is returned if the value does not exist in `$data`

Returns the element at the given position or the original `$data` if `$keys` is empty.

Vale tries different ways to access the element specified in `$keys`. The following variants are tried in this order:

1. `$data[$key]`
2. `$data->$key()`
3. `$data->get$Key()`
4. `$data->get($key)`
5. `$data->has$Key()`
6. `$data->has($key)`
7. `$data->is$Key()`
8. `$data->is($key)`
9. `$data->$key`

### Set

`::set()` and `->setValue()` set the value of an element at the given position.

```php
mixed set(mixed $data, array|string|int $keys, mixed $value)
mixed setValue(mixed $data, array|string|int $keys, mixed $value)
```

- `$data` is an arbitrary data structure
- `$keys` is an array of keys to access the value. If the length is `1`, `$keys` can be a string or int
- `$value` is the value for the element

Returns the modified `$data`

*Set* utilizes the same means of navigating through nested data structures as [Get](#get) and tries the following
variants to set the value:

1. `$data[$key] = $value`
2. `$data->$key($value)`
3. `$data->set$Key($value)`
4. `$data->set($key, $value)`
5. `$data->$key = $value`

### Has

`::has()` and `->hasValue()` returns if an element exists

```php
bool has(mixed $data, array|string|int $keys)
bool hasValue(mixed $data, array|string|int $keys)
```

- `$data` is an arbitrary data structure
- `$keys` is an array of keys to access the value. If the length is `1`, `$keys` can be a string or int

Returns `true` if the element exists, `false` otherwise.

*Has* utilizes the same means of navigating through nested data structures as [Get](#get) and tries the following
variants to check the existence of an element:

1. `isset($data[$key])`
2. `isset($data->$key)`
3. `$data->has$Key()`
4. `$data->has($key)`
5. `$data->is$Key()`
6. `$data->is($key)`
7. `$data->$key()`
8. `$data->get$Key()`

The variants involving a method call (such as `has$Key()` or `has()`) return `true` if the method returns `true` or
a value that evaluates to `true`. If the method returns a value that evaluates to `false` (such as `''`, `0` or `null`)
then *has* returns `false`.

### Remove

`::remove()` and `->removeValue()` remove an element from the given data structure

```php
mixed remove(mixed $data, array|string|int $keys)
mixed removeValue(mixed $data, array|string|int $keys)
```

- `$data` is an arbitrary data structure
- `$keys` is an array of keys to access the value. If the length is `1`, `$keys` can be a string or int

Returns the modified `$data` or `null` if `$keys` is empty

*Remove* utilizes the same means of navigating through nested data structures as [Get](#get) and tries the following
variants to remove the element from the data structure:

1. `unset($data[$key])`
2. `unset($data->$key)`
3. `$data->unset$Key()`
4. `$data->remove$Key()`
5. `$data->remove($key)`

*Please note that `unset()` is not used, because it is an reserved keyword in PHP.*


Change Log
----------

### Version 0.2 (24 March 2015)

- Add `has()` method to check if key exists
- Add `remove()` method to remove key from item
- Improved navigating through complex structures
- Major refactoring, making the code more reusable and testable

### Version 0.1 (15 March 2015)

- Initial release


Motivation
----------

Vale was largely motivated by the need for a simpler, but faster implementation of the
[Symfony PropertyAccess](http://symfony.com/doc/current/components/property_access/introduction.html) component.
PropertyAccess is great when used in templates or config files, that is, code that is compiled and cached before
being executed. However, the heavy use of string parsing and reflection make PropertyAccess not suitable for code that
is not compiled. Another source of inspiration was the [`get-in`](https://github.com/igorw/get-in) library by Igor
Wiedler for array traversal.

Name: I used A Song of Ice and Fire related strings for testing and due to having to write `value` quite often, I came
up with [Vale](http://awoiaf.westeros.org/index.php/Vale_of_Arryn).


Author
------

Vale has been developed by [Florian Eckerstorfer](https://florian.ec) ([Twitter](https://twitter.com/Florian_)) in
Vienna, Europe.

> Vale is a project of [Cocur](http://cocur.co). You can contact us on Twitter:
> [**@cocurco**](https://twitter.com/cocurco)


License
-------

The MIT license applies to Vale. For the full copyright and license information, please view the
[LICENSE](https://github.com/cocur/vale/blob/master/LICENSE) file distributed with this source code.
