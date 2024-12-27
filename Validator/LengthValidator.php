<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Validator;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;

class LengthValidator implements ValidatorInterface
{
    public function validate(ComponentInterface $component, mixed $value): bool
    {
        if (false === is_string($value)) {
            return true;
        }

        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof ComponentViewModelInterface) {
            return true;
        }

        if ($viewModel->hasMinLength() && strlen($value) < $viewModel->getMinLength()) {
            $msg = __('This value should be %1 characters or more in length', $viewModel->getMinLength());
            $viewModel->getMessageManager()->addLocalError((string) $msg);

            return false;
        }

        if ($viewModel->hasMaxLength() && strlen($value) > $viewModel->getMaxLength()) {
            $msg = __('This value should be %1 characters or less in length: '.$value, $viewModel->getMaxLength());
            $viewModel->getMessageManager()->addLocalError((string) $msg);

            return false;
        }

        return true;
    }
}
