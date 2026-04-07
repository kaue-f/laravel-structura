<?php

namespace KaueF\Structura\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Icon
{
    public function __construct(public string $icon) {}
}
