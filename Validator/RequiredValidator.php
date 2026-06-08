<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\Ajax;
use Loki\Components\Util\IsEmpty;
use Loki\Components\Util\SkipValidation;

class RequiredValidator implements ValidatorInterface
{
    public function __construct(
        private readonly IsEmpty $isEmpty,
        private readonly Ajax $ajax,
        private readonly SkipValidation $skipValidation
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === $this->ajax->isAjax()) {
            return true;
        }

        if ($this->skipValidation->isSkipValidation()) {
            return true;
        }

        if ($this->isEmpty->execute($component, $value)) {
            $errorMessage = (string)__('Value is required');
            if ($component instanceof ComponentInterface) {
                $requiredErrorMessage = trim((string)$component->getBlock()->getRequiredErrorMessage());
                if (!empty($requiredErrorMessage)) {
                    $errorMessage = $requiredErrorMessage;
                }
            }

            return [$errorMessage];
        }

        return true;
    }
}
