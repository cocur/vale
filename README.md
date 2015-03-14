Vale
====

> Vale is a helper utility that lets you get and set values in arbitrary nested arrays and objects.
 
Developed by [Florian Eckerstorfer](https://florian.ec) in Vienna, Europe.


Features
--------

Get and set values in complex nested arrays and objects. You can write

```php
$baz = Vale::get($data, ['foo', 'bar', 'baz', 0]);
```

instead of writing:

```php
$baz = (isset($data['foo']->bar['baz'][0])) ? $data['foo']->bar['baz'][0] : null;
```


Installation
------------

You can install Vale using [Composer](https://getcomposer.org):

```shell
$ composer require cocur/vale:dev-master
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
2. `$data->$key`
3. `$data->$key()`
4. `$data->get$Key()`
5. `$data->has$Key()`
6. `$data->is$Key()`

### Set

The `set()` and `setValue()` methods always return the `$data` object or array. If the array or object is nested the
same means of navigating through the array and object as in `get()` are used. When it comes to setting the value
the following versions are tried.

1. `$data[$key] = $value`
2. `$data->$key = $value` (if `$key` already exists in `$data`)
3. `$data->$key($value)`
4. `$data->set$Key($value)`
5. `$data->$key = $value` (if `$key` does not exists in `$data`)

Change Log
----------

*No version released yet.*


License
-------

The MIT license applies to Vale. For the full copyright and license information, please view the 
[LICENSE](https://github.com/cocur/vale/blob/master/LICENSE) file distributed with this source code.
