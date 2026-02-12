<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Dummy;

use Loki\Components\Component\ComponentContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Loki\Components\Component\ComponentContextInterface;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Validator\Validator;

class LengthViewModelDummy implements ComponentViewModelInterface
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
        return ObjectManager::getInstance()->create(AbstractBlock::class);
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
        return ObjectManager::getInstance()->create(ComponentContext::class);
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

    public function isLazyLoad(): bool
    {
        return false;
    }

    public function isAllowRendering(): bool
    {
        return false;
    }

    public function hasBlock(): bool
    {
        return true;
    }

    public function isVisible(): bool
    {
        return true;
    }
}
