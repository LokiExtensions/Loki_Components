<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Magento\Framework\Phrase;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Messages\LocalMessage;
use Loki\Components\Util\Ajax;

class Validator
{
    public function __construct(
        private readonly ValidatorRegistry $validatorRegistry,
        private readonly Ajax $ajax,
    ) {
    }

    public function validate(
        ComponentInterface $component,
        mixed $data = null
    ): bool {
        if ($this->isRequiredButEmpty($component, $data) && false === $this->ajax->isAjax()) {
            return true;
        }

        if (empty($data) && true === in_array('required', $component->getValidators())) {
            return false;
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
        $component->setIsValidated(true);

        foreach ($validators as $validator) {
            $result = $validator->validate($data, $component);
            if (true === $result) {
                continue;
            }

            foreach ($result as $message) {
                if ($message instanceof LocalMessage) {
                    $component->getLocalMessageRegistry()->add($message);
                    continue;
                }

                if ($message instanceof Phrase) {
                    $message = (string)$message;
                }

                if (false === $component->dispatchLocalMessages()) {
                    $component->getGlobalMessageRegistry()->addError($message);
                } else {
                    $component->getLocalMessageRegistry()->addError($message);
                }
            }

            return false;
        }

        return true;
    }

    private function isRequiredButEmpty(ComponentInterface $component, mixed $data): bool
    {
        return empty($data) && in_array('required', $component->getValidators());
    }
}
