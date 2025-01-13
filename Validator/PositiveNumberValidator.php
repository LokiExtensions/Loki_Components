<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;

class PositiveNumberValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component, mixed $value): bool
    {
        if (false === is_numeric($value)) {
            $component->getLocalMessageRegistry()->addError($component->getName(), (string)__('Value must contain numbers only'));

            return false;
        }

        if ($value < 0) {
            $component->getLocalMessageRegistry()->addError($component->getName(), (string)__('Value must be zero or higher'));

            return false;
        }

        return true;
    }
}
