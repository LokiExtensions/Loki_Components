<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Util\Ajax;

class Validator
{
    public function __construct(
        private ValidatorRegistry $validatorRegistry,
        private Ajax $ajax,
    ) {
    }

    public function validate(
        ComponentInterface $component,
        mixed $data = null
    ): bool {
        if (false === $this->ajax->isAjax()) {
            return true;
        }

        if (empty($data) && false === in_array('required', $component->getValidators())) {
            return true;
        }

        if (is_array($data)) {
            foreach ($data as $value) {
                if (false === $this->validate($component, $value)) {
                    return false;
                }
            }

            return true;
        }

        $validators = $this->validatorRegistry->getApplicableValidators($component->getValidators());
        foreach ($validators as $validator) {
            if (false === $validator->validate($component, $data)) {
                return false;
            }
        }

        return true;
    }
}
