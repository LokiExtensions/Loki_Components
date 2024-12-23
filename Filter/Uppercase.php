<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Uppercase implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return strtoupper((string)$value);
    }
}
