<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Filter;

use Yireo\LokiComponents\Component\Filter\FilterInterface;

class Capitalize implements FilterInterface
{
    public function filter(string $value): string
    {
        return ucfirst($value);
    }
}
