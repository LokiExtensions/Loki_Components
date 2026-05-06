<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class RemoveSpaces implements FilterInterface
{
    public function filter(mixed $value, FilterScope $scope): mixed
    {
        return preg_replace('/\s+/', '', (string)$value);
    }
}
