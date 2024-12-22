<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Mutator;

use Yireo\LokiComponents\Component\Filter\FilterRegistry;
use Yireo\LokiComponents\Component\Validator\ValidatorRegistry;

class Context implements ContextInterface
{
    public function __construct(
        private ValidatorRegistry $validatorRegistry,
        private FilterRegistry $filterRegistry
    ) {
    }

    public function getValidatorRegistry(): ValidatorRegistry
    {
        return $this->validatorRegistry;
    }

    public function getFilterRegistry(): FilterRegistry
    {
        return $this->filterRegistry;
    }
}
