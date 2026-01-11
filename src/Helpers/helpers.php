<?php

/**
 * Determines whether a value is null or effectively empty.
 * 
 * Unlike PHP's `empty()` function, this implementation:
 * - Does NOT consider `0` or `"0"` as empty;
 * - Explicitly checks for null;
 * - Treats empty strings or strings containing only whitespace as empty;
 * - Treats empty arrays as empty.
 *
 * @param mixed $value
 * @return bool Returns true if the value is null or empty; otherwise, false.
 */
if (!function_exists('isNullOrEmpty')) {

    function isNullOrEmpty(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            return empty($value) || trim($value) === '';
        }

        if (is_array($value)) {
            return count($value) === 0;
        }

        return false;
    }
}
