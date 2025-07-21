<?php
declare(strict_types=1);

namespace Loki\Components\Filter;

interface FilterInterface
{
    public function filter(mixed $value): mixed;
}
