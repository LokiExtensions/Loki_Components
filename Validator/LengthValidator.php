<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;

class LengthValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === is_string($value)) {
            return true;
        }

        $viewModel = $component->getViewModel();
<<<<<<< HEAD
        if (false === $viewModel instanceof ComponentViewModelInterface) {
            return true;
        }

=======
>>>>>>> 99253294ddb2f8168b8446b37aae118e16a22012
        $minlength = (int)$viewModel->getBlock()->getMinlength();
        if ($minlength > 0 && strlen($value) < $minlength) {
            return [__('Value should have more than %1 characters', $minlength)];
        }

        $maxlength = (int)$viewModel->getBlock()->getMaxlength();
        if ($maxlength > 0 && strlen($value) > $maxlength) {
            return [__('Value should have no more than %1 characters', $maxlength)];
        }

        return true;
    }
}
