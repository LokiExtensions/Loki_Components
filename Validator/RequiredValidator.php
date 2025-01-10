<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;

class RequiredValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component, mixed $value): bool
    {
        echo "REQUIRED: ".$value."\n";
        if (empty($value)) {
            $component->getLocalMessageRegistry()->addError($component, (string)__('Value is required'));

            return false;
        }

        return true;
    }
}
