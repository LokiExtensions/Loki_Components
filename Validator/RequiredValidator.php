<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;

class RequiredValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (empty($value)) {
            return [(string)__('Value is required')];
        }

        return true;
    }
}
