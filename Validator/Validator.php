<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Config\Config;
use Yireo\LokiComponents\Messages\LocalMessage;
use Yireo\LokiComponents\Util\Ajax;

class Validator
{
    public function __construct(
        private ValidatorRegistry $validatorRegistry,
        private Ajax $ajax,
        private Config $config
    ) {
    }

    public function validate(
        ComponentInterface $component,
        mixed $data = null
    ): bool {
        if ($this->config->onlyValidateAjax() && false === $this->ajax->isAjax()) {
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
            $result = $validator->validate($data, $component);
            if (true === $result) {
                continue;
            }

            foreach ($result as $message) {
                if ($message instanceof LocalMessage) {
                    $component->getLocalMessageRegistry()->add($message);
                    continue;
                }

                // @todo: Allow for global message to be added too
                $component->getLocalMessageRegistry()->addError($message);
            }

            return false;
        }

        return true;
    }
}
