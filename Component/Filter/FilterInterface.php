<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Filter;

interface FilterInterface
{
    public function filter(string $value): string;
}
