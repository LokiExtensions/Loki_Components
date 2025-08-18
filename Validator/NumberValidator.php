<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;

class NumberValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === is_numeric($value)) {
            return [(string)__('Value must contain numbers only')];
        }

        return true;
    }
}
