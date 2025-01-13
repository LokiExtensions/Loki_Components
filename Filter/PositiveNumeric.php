<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class PositiveNumeric implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return abs((int)$value);
    }
}
