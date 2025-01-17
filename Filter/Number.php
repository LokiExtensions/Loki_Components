<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Number implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return (int)$value;
    }
}
