<?php

namespace KaueF\Structura\Support;

use JsonSerializable;
use KaueF\Structura\Concerns\InteractsWithData;

/**
 * Optional abstract base for immutable Data classes.
 *
 * Prefer the InteractsWithData trait when inheritance is not appropriate.
 */
abstract readonly class DataSupport implements JsonSerializable
{
    use InteractsWithData;
}
