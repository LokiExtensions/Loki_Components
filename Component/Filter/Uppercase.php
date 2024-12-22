<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Filter;

use Yireo\LokiComponents\Component\Filter\FilterInterface;

class Uppercase implements FilterInterface
{
    public function filter(string $value): string
    {
        return strtoupper($value);
    }
}
