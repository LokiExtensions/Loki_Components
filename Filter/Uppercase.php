<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;

class Uppercase implements FilterInterface
{
    public function filter(mixed $value, ?ComponentInterface $component = null, ?string $scope = null): mixed
    {
        return strtoupper((string)$value);
    }
}
