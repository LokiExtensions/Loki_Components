<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;

class NumberValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component, mixed $value): bool
    {
        $value = $component->getValue();
        if (false === is_numeric($value)) {
            $component->getMessages()->addError((string)__('Value must contain numbers only'));

            return false;
        }

        return true;
    }
}
