Vale
====

> Vale lets you get and set values in arbitrary nested arrays and objects.

[![Build Status](https://img.shields.io/travis/cocur/vale/master.svg?style=flat)](https://travis-ci.org/cocur/vale)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/cocur/vale.svg?style=flat)](https://scrutinizer-ci.com/g/cocur/vale/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/cocur/vale.svg?style=flat)](https://scrutinizer-ci.com/g/cocur/vale/?branch=master)
 
Developed by [Florian Eckerstorfer](https://florian.ec) in Vienna, Europe.


Features
--------

Get and set values in complex nested arrays and objects. You can write

```php
$baz = Vale::get($data, ['foo', 'bar', 'baz', 0]);
```

instead of writing

```php
$baz = (isset($data['foo']->bar['baz'][0])) ? $data['foo']->bar['baz'][0] : null;
```


Installation
------------

You can install Vale using [Composer](https://getcomposer.org):

```shell
$ composer require cocur/vale
```


Usage
-----

You can use the static `get()` and `set()` methods.

```php
use Cocur\Vale\Vale;

Vale::get(['name' => 'Tyrion'], ['name']); // -> "Tyrion"
Vale::set([], ['name'], 'Tyrion'); // -> ["name" => "Tyrion"]
```

In addition you can create an instance of `Vale` and use the `getValue()` and `setValue()` methods:

```php
use Cocur\Vale\Vale;

$vale = new Vale();
$vale->getValue(['name' => 'Tyrion'], ['name']); // -> "Tyrion"
$vale->setValue([], ['name'], 'Tyrion'); // -> ["name" => "Tyrion"]
```


Documentation
-------------

### Get

If the `$keys` parameter is an empty array, an empty string or `null`, the original `$data` is returned.

```php
Vale::get(['name' => 'Florian'], []); // -> ['name' => 'Florian']
```

If `$data` is an array, each element in `$keys` is used to navigate deeper into the nested `$data` array; if `$data`
is an object, each key is tried as property, method, getter, hasser and isser. The order is like this:

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

The `set()` and `setValue()` methods always return the `$data` object or array. If the array or object is nested the
same means of navigating through the array and object as in `get()` are used. When it comes to setting the value
the following versions are tried.

1. `$data[$key] = $value`
2. `$data->$key($value)`
3. `$data->set$Key($value)`
4. `$data->set($key, $value)`
5. `$data->$key = $value`

### Has

The `has()` and `hasValue()` methods return `true` if the given key exists in the array or object and `false` if the
key does not exist. They always return `true` if `$keys` is empty. `hasValue()` checks the following versions:

1. `isset($data[$key])`
2. `isset($data->$key)`
3. `$data->has$Key()`
4. `$data->has($key)`
5. `$data->is$Key()`
6. `$data->is($key)`
7. `$data->$key()`
8. `$data->get$Key()`

### Remove

The `remove()` and `removeValue()` method removes the given key from the array or object. If the keys are empty then
`null` is returned (that is, they remove the whole input). The following versions are tried:

1. `unset($data[$key])`
2. `unset($data->$key)`
3. `$data->unset$Key()`
4. `$data->remove$Key()`
5. `$data->remove($key)`


Change Log
----------

### Version 0.1 (15 March 2015)

- Initial release


License
-------

The MIT license applies to Vale. For the full copyright and license information, please view the 
[LICENSE](https://github.com/cocur/vale/blob/master/LICENSE) file distributed with this source code.
