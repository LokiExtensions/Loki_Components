<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Lowercase implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return strtolower((string)$value);
    }
}
