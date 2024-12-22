<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator\Component;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\Validator\ValidatorInterface;

class RequiredValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component): bool
    {
        $value = $component->getValue();
        if (empty($value)) {
            $component->getMessages()->addError((string)__('Value is required'));

            return false;
        }

        return true;
    }
}
