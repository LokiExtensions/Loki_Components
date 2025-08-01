<?php declare(strict_types=1);

namespace Loki\Components\Validator;

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
     * @param array $validators
     *
     * @return ValidatorInterface[]
     */
    public function getApplicableValidators(array $validators = []): array
    {
        $applicableValidators = [];
        foreach ($validators as $validatorName) {
            if (array_key_exists($validatorName, $this->validators)) {
                $applicableValidators[$validatorName] = $this->validators[$validatorName];
            }
        }

        return $applicableValidators;
    }
}
