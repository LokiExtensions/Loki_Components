<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Security implements FilterInterface
{
    public function filter(mixed $value): mixed
    {
        $value = (string)$value;
        $value = strip_tags($value);
        $value = htmlentities($value);
        return $value;
    }
}
