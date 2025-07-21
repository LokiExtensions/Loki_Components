<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class Lowercase implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return strtolower((string)$value);
    }
}
