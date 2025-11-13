<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;

class Security implements FilterInterface
{
    public function filter(mixed $value, ?ComponentInterface $component = null, ?string $scope = null): mixed
    {
       if (is_object($value)) {
            return $value;
        }

        $value = (string)$value;
        //$value = strip_tags($value);
        $value = htmlspecialchars_decode($value);
        return $value;
    }
}
