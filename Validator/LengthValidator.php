<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentRepositoryInterface;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;

class LengthValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): true|array
    {
        if (false === is_string($value)) {
            return true;
        }

        $viewModel = $component->getViewModel();
        if ($viewModel->hasMinLength() && strlen($value) < $viewModel->getMinLength()) {
            return [__('Value should have more than %1 characters', $viewModel->getMinLength())];
        }

        if ($viewModel->hasMaxLength() && strlen($value) > $viewModel->getMaxLength()) {
            return [__('Value should have no more than %1 characters', $viewModel->getMaxLength())];
        }

        return true;
    }
}
