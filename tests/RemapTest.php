<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RemapTest extends TestCase
{
    public function testCallable(): void
    {
        $this->assertIsCallable('array_remap', 'array_remap is not callable.');
    }

    /**
     * Basic test - swap indexes.
     */
    public function testBasicMapping(): void
    {
        $input = [
            'a' => 'aaa',
            'b' => 'bbb',
        ];
        $this->assertSame(array_flip($input), array_remap(function (string $val, string $key) {
            return [$val => $key];
        }, $input));
    }

    /**
     * Mapping to numeric indexes is also possible.
     */
    public function testNumericIndices(): void
    {
        $input = [
            1 => ['aaa', -1],
            2 => ['bbb', 40],
        ];
        $expect = [
            0 => 'aaa',
            42 => 'bbb',
        ];
        $this->assertSame($expect, array_remap(function (array $val, int $key) {
            [$str, $num] = $val;
            return [$key + $num => $str];
        }, $input));
    }

    /**
     * When the same indexes are returned, the latter values overwrite the former ones.
     * Note that the elements will be ordered in the order the indexes occurred.
     */
    public function testOverwrite(): void
    {
        $input = [
            ['id' => 1, 'size' => 'XL'],
            ['id' => 2, 'size' => 'L'],
            ['id' => 3, 'size' => 'M'],
            ['id' => 5, 'size' => 'M'],
            ['id' => 4, 'size' => 'XL'],
        ];
        $expect = [
            'XL' => 4,
            'L' => 2,
            'M' => 5,
        ];
        $this->assertSame($expect, array_remap(function (array $val) {
            return [
                $val['size'] => $val['id'],
            ];
        }, $input));
    }

    /**
     * When the same indexes are returned, the latter values overwrite the former ones.
     * Note that the elements will be ordered in the order the indexes occurred.
     */
    public function testOverwriteNumeric(): void
    {
        $input = [
            ['id' => 1, 'size' => 100],
            ['id' => 2, 'size' => 200],
            ['id' => 3, 'size' => 300],
            ['id' => 5, 'size' => 300],
            ['id' => 4, 'size' => 100],
        ];
        $expect = [
            100 => 4,
            200 => 2,
            300 => 5,
        ];
        $this->assertSame($expect, array_remap(function (array $val) {
            return [
                $val['size'] => $val['id'],
            ];
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
            42 => 'aaa+',
            'B' => 'bbb-',
        ];
        $this->assertSame($expect, array_remap(function (string $val, /*string $key,*/ string $arg1, int $arg2) {
            return $arg2 > 100 ?
                [strtoupper($val[0]) => "{$val}{$arg1}"] :
                [$arg2 => "{$val}{$arg1}"];
        }, $input, $arg1, $arg2));

        $expect = [
            42 => 'aaa+a',
            123 => 'bbb-b',
        ];
        $this->assertSame($expect, array_remap(function (string $val, string $key, string $arg1, int $arg2) {
            return [$arg2 => "{$val}{$arg1}{$key}"];
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
        $this->assertSame($expect, array_remap($throw, $input));
        // and the last argument needs not be an array in this case either
        $this->assertSame($expect, array_remap($throw, $input, 'foo'));
    }

    public function testInvalidReturnType(): void
    {
        $this->expectException(LogicException::class);
        array_remap(function () {
            return 'scalar'; // invalid mapper output
        }, [42]);
    }
}
