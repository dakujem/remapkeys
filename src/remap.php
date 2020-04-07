<?php

declare(strict_types=1);

if (!function_exists('array_remap')) {
    /**
     * Remaps an array.
     *
     * The iteratee mapper function must return an array containing a single element with an index
     * in the following form: [index => value].
     *
     * The iteratee receives the value and original index as arguments,
     * if only the value is passed to the remap call.
     * In case multiple arguments are passed,
     * these are used to populate further arguments to the iteratee invocations.
     *
     * The resulting array will consist of the values and their respective indexes returned by the iteratee.
     * If the same index is returned by the iteratee invocations, the latter values replace the former ones.
     *
     * @param callable $mapper iteratee
     * @param array $values
     * @param array ...$args arrays of additional arguments to the iteratee
     * @return array
     */
    function array_remap(callable $mapper, array $values, ...$args): array
    {
        if (count($values) > 0) {
            if (count($args) > 0) {
                // Note that if multiple arguments are passed, the iteratee does not receive the keys by default:
                $mapped = array_map($mapper, $values, ...$args);
            } else {
                // Otherwise the keys are passed as the second argument to the iteratee invocations:
                $mapped = array_map($mapper, $values, array_keys($values));
            }
            return array_reduce($mapped, function (array $carry, /*array*/ $pair): array {
                if (!is_array($pair) || count($pair) !== 1) {
                    // Throw an exception here, as the PHP default type-related error may be hard to understand.
                    throw new LogicException(sprintf('The mapper function must return a single pair in form of an array [index => value], %s returned.', is_object($pair) ? 'an instance of ' . get_class($pair) : gettype($pair)));
                }
                return array_replace($carry, $pair);
            }, []);
        }
        return [];
    }
}
