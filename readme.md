
# Remap Keys

[![Tests](https://github.com/dakujem/remapkeys/actions/workflows/php-test.yml/badge.svg)](https://github.com/dakujem/remapkeys/actions/workflows/php-test.yml)
<!--
[![Build Status](https://travis-ci.org/dakujem/remapkeys.svg?branch=master)](https://travis-ci.org/dakujem/remapkeys)
-->

> 💿 `composer require dakujem/remapkeys`

### Functions for common array operations.

This package adds a pair of functions similar to `array_map`
that are commonly used when working with arrays:
- `array_remap`
  - like `array_map`, but allows to specify/map indexes of the result
- `array_map_keys`
  - like `array_map`, but passes indexes to the iteratee function and preserves indexes in the result


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
