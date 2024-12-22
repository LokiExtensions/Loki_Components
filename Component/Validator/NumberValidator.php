<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator\Component;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\Validator\ValidatorInterface;

class NumberValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component): bool
    {
        $value = $component->getValue();
        if (false === is_numeric($value)) {
            $component->getMessages()->addError((string)__('Value must contain numbers only'));

            return false;
        }

        return true;
    }
}
