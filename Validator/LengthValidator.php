<?php declare(strict_types=1);

namespace Loki\Components\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\View\Element\AbstractBlock;

class LengthValidator implements ValidatorInterface
{
    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if (false === is_string($value)) {
            return true;
        }

        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof ComponentViewModelInterface) {
            return true;
        }

        $minlength = $this->getMin($viewModel->getBlock());
        if ($minlength > 0 && strlen($value) < $minlength) {
            return [__('Value should have more than %1 characters', $minlength)];
        }

        $maxlength = $this->getMax($viewModel->getBlock());
        if ($maxlength > 0 && strlen($value) > $maxlength) {
            return [__('Value should have no more than %1 characters', $maxlength)];
        }

        return true;
    }

    private function getMin(AbstractBlock $block): int
    {
        $possibleValues = ['min', 'minlength'];
        foreach ($possibleValues as $possibleValue) {
            if ((int)$block->getData($possibleValue) > 0) {
                return (int)$block->getData($possibleValue);
            }

            $fieldAttributes = (array)$block->getFieldAttributes();
            if (!empty($fieldAttributes[$possibleValue])) {
                return $fieldAttributes[$possibleValue];
            }
        }

        return 0;
    }

    private function getMax(AbstractBlock $block): int
    {
        $possibleValues = ['max', 'maxlength'];
        foreach ($possibleValues as $possibleValue) {
            if ((int)$block->getData($possibleValue) > 0) {
                return (int)$block->getData($possibleValue);
            }

            $fieldAttributes = (array)$block->getFieldAttributes();
            if (!empty($fieldAttributes[$possibleValue])) {
                return $fieldAttributes[$possibleValue];
            }
        }

        return 0;
    }
}
