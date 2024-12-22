<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Filter;

use Yireo\LokiComponents\Component\Filter\FilterInterface;

class Trim implements FilterInterface
{
    public function filter(string $value): string
    {
        return trim($value);
    }
}
