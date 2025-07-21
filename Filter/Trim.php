<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class Trim implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return trim((string)$value);
    }
}
