<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

interface FilterInterface
{
    public function filter(mixed $value): mixed;
}
