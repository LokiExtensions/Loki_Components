<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;

class LengthValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component, mixed $value): bool
    {
        $value = $component->getValue();
        if (false === is_string($value)) {
            return true;
        }

        if ($component->hasMinLength() && strlen($value) < $component->getMinLength()) {
            $msg = __('This value should be %1 characters or more in length', $component->getMinLength());
            $component->getMessages()->addError((string) $msg);

            return false;
        }

        if ($component->hasMaxLength() && strlen($value) > $component->getMaxLength()) {
            $msg = __('This value should be %1 characters or less in length: '.$value, $component->getMaxLength());
            $component->getMessages()->addError((string) $msg);

            return false;
        }

        return true;
    }
}
