Getter
======

> `get()` values from arbitrary data structures (such as arrays, objects and mixtures). Allows also `set()` and
 `has()` values.
 
Developed by [Florian Eckerstorfer](https://florian.ec) in Vienna, Europe.

Usage
-----

Assume we have the following object and there exists a `getFirstName()` method.

```
Person Object (
    [firstName:Person:private] => Cersei
    [children] => Array (
        [0] => Person Object (
            [firstName:Person:private] => Joffrey
            [children] => Array ()
        )
    )
)
```

We can use Getter to retrieve the first name of the first child:

```php
use Cocur\Getter\Getter;

$firstChildName = Getter::get($cersei, ['children', 0, 'firstName']);
```
