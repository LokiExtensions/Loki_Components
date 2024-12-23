<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Trim implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        return trim((string)$value);
    }
}
