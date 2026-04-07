<?php

namespace KaueF\Structura\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Label
{
    public function __construct(public string $label) {}
}
