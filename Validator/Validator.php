<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Magento\Framework\Phrase;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Messages\LocalMessage;

class Validator
{
    public function __construct(
        private readonly ValidatorRegistry $validatorRegistry,
    ) {
    }

    public function validate(
        ComponentInterface $component,
        mixed $data = null,
        string $scope = ''
    ): bool {
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
}
