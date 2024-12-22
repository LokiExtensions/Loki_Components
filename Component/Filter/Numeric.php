<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Filter;

use Yireo\LokiComponents\Component\Filter\FilterInterface;

class Numeric implements FilterInterface
{
    public function filter(string $value): string
    {
        return preg_replace('/[^0-9 ]/i', '', $value);
    }
}
