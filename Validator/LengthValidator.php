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
            return [__('This value should be %1 characters or more in length', $viewModel->getMinLength())];
        }

        if ($viewModel->hasMaxLength() && strlen($value) > $viewModel->getMaxLength()) {
            return [__('This value should be %1 characters or less in length: '.$value, $viewModel->getMaxLength())];
        }

        return true;
    }
}
