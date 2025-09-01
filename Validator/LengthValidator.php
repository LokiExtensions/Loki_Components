<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\Behaviour\LengthBehaviourInterface;
use Loki\Components\Component\ComponentInterface;

class LengthValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === is_string($value)) {
            return true;
        }

        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof LengthBehaviourInterface) {
            return true;
        }

        $minlength = (int)$viewModel->getBlock()->getMinlength();
        if ($minlength > 0 && strlen($value) < $minlength) {
            return [__('Value should have more than %1 characters', $minlength)];
        }

        $maxlength = (int)$viewModel->getBlock()->getMaxlength();
        if ($maxlength && strlen($value) > $maxlength) {
            return [__('Value should have no more than %1 characters', $maxlength)];
        }

        return true;
    }
}
