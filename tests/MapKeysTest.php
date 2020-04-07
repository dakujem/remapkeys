<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MapKeysTest extends TestCase
{
    public function testCallable(): void
    {
        $this->assertIsCallable('array_map_keys', 'array_map_keys is not callable.');
    }

    /**
     * Preserve associative indexes.
     */
    public function testBasicMapping(): void
    {
        $input = [
            'a' => 'aaa',
            'b' => 'bbb',
        ];
        $expect = [
            'a' => 'aaaA',
            'b' => 'bbbB',
        ];
        $this->assertSame($expect, array_map_keys(function (string $val, string $key) {
            return $val . strtoupper($key);
        }, $input));
    }

    /**
     * Numeric indexes are also preserved.
     */
    public function testNumericIndices(): void
    {
        $input = [
            0 => 'aaa',
            42 => 'bbb',
        ];
        $expect = [
            0 => 'Aaa0',
            42 => 'Bbb42',
        ];
        $this->assertSame($expect, array_map_keys(function (string $val, string $key) {
            return ucfirst($val) . $key;
        }, $input));
    }

    /**
     * When passing additional arguments, the indexes are NOT passed.
     * You need to use array_keys if you want them.
     */
    public function testAdditionalArguments(): void
    {
        $input = [
            'a' => 'aaa',
            'b' => 'bbb',
        ];
        $arg1 = [
             '+',
             '-',
        ];
        $arg2 = [
            'a' => 42,
            'b' => 123,
        ];

        $expect = [
            'a' => 'aaa+42',
            'b' => 'bbb-123',
        ];
        $this->assertSame($expect, array_map_keys(function (string $val, /*string $key,*/ string $arg1, int $arg2) {
            return "{$val}{$arg1}{$arg2}";
        }, $input, $arg1, $arg2));

        $expect = [
            'a' => 'aaa+42a',
            'b' => 'bbb-123b',
        ];
        $this->assertSame($expect, array_map_keys(function (string $val, string $key, string $arg1, int $arg2) {
            return "{$val}{$arg1}{$arg2}{$key}";
        }, $input, array_keys($input), $arg1, $arg2));
    }

    public function testEdgeCases(): void
    {
        $input = [];
        $expect = [];
        $throw = function () {
            throw new LogicException('This should not be thrown.');
        };
        // shows 2 things: 1/ an empty array is returned, 2/ the iteratee is never invoked
        $this->assertSame($expect, array_map_keys($throw, $input));
        // and the last argument needs not be an array in this case either
        $this->assertSame($expect, array_map_keys($throw, $input, 'foo'));
    }
}
