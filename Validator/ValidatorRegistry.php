<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use InvalidArgumentException;

class ValidatorRegistry
{
    public function __construct(
        private array $validators = []
    ) {
        foreach ($this->validators as $validator) {
            if (false === $validator instanceof ValidatorInterface) {
                throw new InvalidArgumentException('Validator instance is not supported');
            }
        }
    }

    /**
     * @param array $selectedValidators
     *
     * @return ValidatorInterface[]
     * @todo: Rename to getRequestedValidators()
     */
    public function getSelectedValidators(array $selectedValidators = []): array
    {
        $validators = [];
        foreach ($selectedValidators as $validatorName) {
            if (array_key_exists($validatorName, $this->validators)) {
                $validators[$validatorName] = $this->validators[$validatorName];
            }
        }

        return $validators;
    }
}
