<?php

namespace KaueF\Structura\Support;

use UnitEnum;
use BackedEnum;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class EnumSupport
{
    /**
     * Resolves and returns the enum class name.
     *
     * Accepts either an enum instance or a class-string
     * and normalizes it to the enum class name.
     *
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @return string Fully qualified enum class name.
     */
    protected static function class(UnitEnum|string $enum): string
    {
        return $enum instanceof UnitEnum
            ? $enum::class
            : $enum;
    }

    /**
     * Returns the scalar value of an enum case.
     *
     * For BackedEnum, returns the backed value.
     * For pure enums, returns the case name.
     *
     * @param UnitEnum $case Enum case.
     * @return int|string Enum value or name.
     */
    protected static function value(UnitEnum $case): int|string
    {
        return $case instanceof BackedEnum
            ? $case->value
            : $case->name;
    }

    /**
     * Converts enum cases to an associative array.
     *
     * Result format:
     * [value => name]
     *
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @return array Associative array of enum values and names.
     */
    public static function toArray(UnitEnum|string $enum): array
    {
        return array_combine(
            keys: self::values(self::class($enum)),
            values: self::names(self::class($enum)),
        );
    }

    /**
     * Converts enum cases into a structured data array.
     *
     * * Result format:
     * [
     *   ['id' => value, 'name' => label],
     * ]
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param string $labelMethod Method used to resolve the label.
     * @param callable|null $callback Optional case filter.
     * @param string $sortBy Field used for sorting ('id' or 'name').
     * @param string $order Sort order ('asc' or 'desc').
     * @return array Normalized enum data.
     */
    public static function toData(UnitEnum|string $enum, string $labelMethod = 'label', ?callable $callback = null, string $sortBy = 'name', $order = 'asc'): array
    {
        $enum = self::class($enum);

        return collect($enum::cases())
            ->filter(fn(UnitEnum $case) => ! $callback || $callback($case))
            ->map(fn(UnitEnum $case) => [
                'id' => self::value($case),
                'name' => method_exists($case, $labelMethod)
                    ? $case->{$labelMethod}()
                    : $case->name
            ])
            ->sortBy($sortBy, SORT_NATURAL, $order === 'desc')
            ->values()
            ->toArray();
    }

    /**
     * Returns the labels of all enum cases.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param string|null $order Optional sort order ('asc' or 'desc').
     * @return array List of labels.
     */
    public static function labels(UnitEnum|string $enum, ?string $order = null): array
    {
        $enum = self::class($enum);

        return self::orderBy(
            array: array_map(fn($e) => $e->label(), $enum::cases()),
            sort: $order
        );
    }

    /**
     * Returns the names of all enum cases.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param string|null $order Optional sort order ('asc' or 'desc').
     * @return array List of case names.
     */
    public static function names(UnitEnum|string $enum, ?string $order = null): array
    {
        $enum = self::class($enum);

        return self::orderBy(
            array: array_column($enum::cases(), 'name'),
            sort: $order
        );
    }

    /**
     * Returns the values of all enum cases.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param string|null $order Optional sort order ('asc' or 'desc').
     * @return array List of case values.
     */
    public static function values(UnitEnum|string $enum, ?string $order = null): array
    {
        $enum = self::class($enum);

        return self::orderBy(
            array: array_map(fn($case) => self::value($case), $enum::cases()),
            sort: $order
        );
    }

    /**
     * Sorts an array based on the given direction.
     * 
     * @param array $array Array to be sorted.
     * @param string|null $sort Sort direction ('asc', 'desc' or null).
     * @return array Sorted or original array.
     */
    protected static function orderBy(array $array, ?string $sort = null): array
    {
        return match ($sort) {
            'asc' => Arr::sort($array),
            'desc' =>  Arr::sortDesc($array),
            default => $array,
        };
    }

    /**
     * Converts enum cases to a JSON.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @return string JSON.
     */
    public static function toJson(UnitEnum|string $enum): string
    {
        return json_encode(self::toArray(enum: $enum));
    }

    /**
     * Validation rule based on enum values
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @return \Illuminate\Validation\Rules\In Validation rule.
     */
    public static function validationRule(UnitEnum|string $enum): \Illuminate\Validation\Rules\In
    {
        return Rule::in(self::values(enum: $enum));
    }

    /**
     * Resolves an enum case by its name.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param string $name Case name.
     * @return UnitEnum Enum case.
     */
    public static function fromName(UnitEnum|string $enum, string $name): UnitEnum
    {
        $enum = self::class($enum);
        return constant($enum . '::' . $name);
    }

    /**
     * Attempts to resolve an enum case by its name.
     *
     * Returns null if the case does not exist.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param string $name Case name.
     * @return UnitEnum|null Enum case or null.
     */
    public static function tryFromName(UnitEnum|string $enum, string $name): ?UnitEnum
    {
        $enum = self::class($enum);

        return defined($enum . '::' . $name)
            ? constant($enum . '::' . $name)
            : null;
    }

    /**
     * Checks whether two enum values are identical.
     * 
     * @param UnitEnum|string $enumA Enum value to compare.
     * @param UnitEnum $enumB Reference enum value.
     * @return bool True if both are identical.
     */
    public static function equals(UnitEnum|string $enum_a, UnitEnum $enum_b): bool
    {
        return $enum_a === $enum_b;
    }

    /**
     * Determines if any enum value exists in the given array.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param array $enumArray Array of values to check.
     * @return bool True if a match is found.
     */
    public static function in(UnitEnum|string $enum, array $enum_array): bool
    {
        foreach ($enum::cases() as $case) {
            if (in_array($case->value, $enum_array, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a random enum case.
     * 
     * Allows excluding one or more values.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param UnitEnum|BackedEnum|array|string|null $except Values to exclude.
     * @return UnitEnum Random enum case.
     */
    public static function random(UnitEnum|string $enum, UnitEnum|BackedEnum|null|array|string $except = null): UnitEnum
    {
        $enum = self::class($enum);
        $except = array_filter((array) $except, fn($e) => $e !== null);

        $cases = array_filter(
            $enum::cases(),
            fn($case): bool => ! in_array(self::value($case), $except, true)
        );

        return $cases[array_rand($cases)];
    }

    /**
     * Returns the value of a random enum case.
     * 
     * @param UnitEnum|string $enum Enum instance or enum class name.
     * @param UnitEnum|BackedEnum|array|string|null $except Values to exclude.
     * @return int|string Random enum value.
     */
    public static function randomValue(UnitEnum|string $enum, UnitEnum|BackedEnum|null|array|string $except = null): int|string
    {
        return self::value(
            self::random(enum: $enum, except: $except)
        );
    }
}
