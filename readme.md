
# Remap Keys

[![Tests](https://github.com/dakujem/remapkeys/actions/workflows/php-test.yml/badge.svg)](https://github.com/dakujem/remapkeys/actions/workflows/php-test.yml)
[![Coverage Status](https://coveralls.io/repos/github/dakujem/remapkeys/badge.svg?branch=feat/coveralls)](https://coveralls.io/github/dakujem/remapkeys?branch=feat/coveralls)

> 💿 `composer require dakujem/remapkeys`


This package adds a pair of functions similar to `array_map`
that are commonly used when working with arrays:
- `array_remap`
  - like `array_map`, but allows to specify/map indexes of the result
- `array_map_keys`
  - like `array_map`, but passes indexes to the iteratee function and preserves indexes in the result


## Toru (alternative)

Both functions provided by this package can be replaced by utils provided by [Toru 取る (`dakujem/toru`)](https://github.com/dakujem/toru),
which also offers tools to work with generic `iterable` type.

The `array_remap` can be replaced by less restrictive `Itera::unfold`:
```php
// Original `array_remap` function call:
array_remap($function, $input);

// Replaced by `Itera` class method call:
Itera::unfold($input, $function);
```

Pros:
- also enable to one value into multiple
- enable including branching logic (`if`) inside the mapper
- may be more memory efficient, especially for large arrays


The `array_map_keys` can be replaced by `Itera::map` or `Itera::apply`, because all callables in Toru receive keys along with values:
```php
// Original `array_map_keys` function call:
array_map_keys($function, $input);

// Replaced by `Itera` class method call:
Itera::map($input, $function);
```

Pros:
- may be more memory efficient, especially for large arrays


## `array_remap`

Allows re-mapping both indices and values of arrays using a mapper function.

```php
$input = [
    'foo' => 'bar',
    'b' => 'Bran',
    'unknown' => 'Stark',
];
array_remap(function($val, $index){
    return [ strtolower($val) => strlen($index) ];
}, $input);

/* result:
[
    'bar' => 3,
    'bran' => 1,
    'stark' => 7,
]
*/
```

```php
$input = [
    [
        'url' => 'https://www.google.com',
        'provider' => 'Google'
    ],
    [
        'url' => 'https://www.yahoo.com',
        'provider' => 'Yahoo!'
    ],
];
array_remap(function($val){
    return [ $val['url'] => $val['provider'] ];
}, $input);

/* result:
[
    'https://www.google.com' => 'Google',
    'https://www.yahoo.com' => 'Yahoo!',
]
*/
```

> Internally, this is a map-reduce operation.

See [the source](/src/remap.php) for more details.


## `array_map_keys`

Allows to work with both array values and their indexes.
The indexes are preserved in the result.

```php
$input = [
    'foo' => 'bar',
    'boring' => 'Bran',
    'strange' => 'Stark',
];
array_map_keys(function($val, $index){
    return ucfirst($index) . ' ' . ucfirst($val);
}, $input);

/* result:
[
    'foo' => 'Foo Bar',
    'boring' => 'Boring Bran',
    'strange' => 'Strange Stark',
]
*/
```

> Note that one could natively call `array_map($values, array_keys($values))`, but that call does _not_ preserve the original keys.

See [the source](/src/map.php) for more details.


## Why

These two fill the gap in PHP core for commonly occurring operations when the indexes are used during mapping.\
A seemingly simple task, it has its caveats when implementing, though.
