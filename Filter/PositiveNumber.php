<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class PositiveNumber implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return abs((int)$value);
    }
}
