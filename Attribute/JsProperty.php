<?php declare(strict_types=1);

namespace Loki\Components\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class JsProperty
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
