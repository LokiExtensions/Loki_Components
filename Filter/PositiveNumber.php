<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class PositiveNumber implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        if ($value > 0) {
            return $value;
        }

        return 0;
    }
}
