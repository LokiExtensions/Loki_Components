<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\Ajax;
use Loki\Components\Util\IsEmpty;

class RequiredValidator implements ValidatorInterface
{
    public function __construct(
        private readonly IsEmpty $isEmpty,
        private readonly Ajax $ajax,
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === $this->ajax->isAjax()) {
            return true;
        }

        if ($this->isEmpty->execute($component, $value)) {
            return [(string)__('Value is required')];
        }

        return true;
    }
}
