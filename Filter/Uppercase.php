<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class Uppercase implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return strtoupper((string)$value);
    }
}
