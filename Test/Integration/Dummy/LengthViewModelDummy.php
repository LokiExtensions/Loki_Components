<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Dummy;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentContext;
use Yireo\LokiComponents\Component\ComponentContextInterface;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;
use Yireo\LokiComponents\Filter\Filter;
use Yireo\LokiComponents\Validator\Validator;
use Yireo\LokiComponents\Component\Behaviour\LengthBehaviourInterface;

class LengthViewModelDummy implements ComponentViewModelInterface, LengthBehaviourInterface
{
    public function setComponent(ComponentInterface $component): void
    {
    }

    public function setValidator(Validator $validator): void
    {
    }

    public function setFilter(Filter $filter): void
    {
    }

    public function setBlock(AbstractBlock $block): void
    {
    }

    public function getElementId(): string
    {
        return '';
    }

    public function getBlock(): AbstractBlock
    {
        return null;
    }

    public function getValue(): mixed
    {
        return null;
    }

    public function getValidators(): array
    {
        return [];
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getTargets(): array
    {
        return [];
    }

    public function getTargetString(): string
    {
        return '';
    }

    public function getMessages(): array
    {
        return [];
    }

    public function getContext(): ComponentContextInterface
    {
        return new ComponentContext();
    }

    public function getTemplate(): ?string
    {
        return '';
    }

    public function getComponentName(): string
    {
        return '';
    }

    public function getJsComponentName(): ?string
    {
        return '';
    }

    public function getJsData(): array
    {
        return [];
    }

    public function getMinLength(): int
    {
        return false;
    }

    public function hasMinLength(): bool
    {
        return false;
    }

    public function getMaxLength(): int
    {
        return 0;
    }

    public function hasMaxLength(): bool
    {
        return 0;
    }
}
