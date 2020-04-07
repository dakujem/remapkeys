<?php

declare(strict_types=1);

if (!function_exists('array_map_keys')) {
    /**
     * Calls a mapper function (iteratee) for each element of an array.
     * The original keys are preserved.
     * The iteratee function receives the array element value and its index as arguments.
     *
     * Behaves as standard `array_map` does, except the second argument to the iteratee is the key.
     *
     * Note that this is different to calling `array_map($iteratee, $values, array_key($values))`
     * in that the keys are preserved in the result array.
     *
     * In case the function is invoked with multiple arguments, these replace the key argument to the iteratee.
     * This is again analogous to what passing arguments to the `array_map` does.
     * The result array keys are still preserved.
     *
     * @param callable $mapper iteratee
     * @param array $values
     * @param array ...$args arrays of additional arguments to the iteratee
     * @return array
     */
    function array_map_keys(callable $mapper, array $values, ...$args): array
    {
        if (count($values) > 0) {
            // Note that if multiple arguments are passed, the iteratee does not receive the keys by default:
            if (count($args) > 0) {
                return array_combine(array_keys($values), array_map($mapper, $values, ...$args));
            }
            // Otherwise the keys are passed as the second argument to the iteratee invocations:
            $keys = array_keys($values);
            return array_combine($keys, array_map($mapper, $values, $keys));
        }
        return [];
    }
}
