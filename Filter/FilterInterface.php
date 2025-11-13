<?php
declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;

interface FilterInterface
{
    public function filter(mixed $value, ?ComponentInterface $component = null, ?string $scope = null): mixed;
}
