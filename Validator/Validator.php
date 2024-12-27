<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;

class Validator
{
    public function __construct(
        private ValidatorRegistry $validatorRegistry,
    ) {
    }

    public function validate(
        ComponentInterface $component,
        mixed $data = null
    ): bool {
        if (empty($data)) {
            return true;
        }

        if (is_array($data)) {
            foreach ($data as $value) {
                if (false === $this->validate($value)) {
                    return false;
                }
            }

            return true;
        }

        $validators = $this->validatorRegistry->getSelectedValidators($component->getValidators());
        foreach ($validators as $validator) {
            if (false === $validator->validate($component, $data)) {
                return false;
            }
        }

        return true;
    }
}
