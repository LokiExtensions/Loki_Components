<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\LokiComponents\Filter\Filter;
use Yireo\LokiComponents\Validator\Validator;

interface ComponentViewModelInterface extends ArgumentInterface
{
    public function setComponent(ComponentInterface $component): void;

    public function setValidator(Validator $validator): void;

    public function setFilter(Filter $filter): void;

    public function setBlock(AbstractBlock $block): void;

    public function getElementId(): string;

    public function getBlock(): AbstractBlock;

    public function getValue(): mixed;

    public function getValidators(): array;

    public function getFilters(): array;

    public function getTargets(): array;

    public function getTargetString(): string;

    public function getMessages(): array;

    public function getContext(): ComponentContextInterface;

    public function getTemplate(): ?string;

    public function getComponentName(): string;

    public function isLazyLoad(): bool;

    public function isAllowRendering(): bool;

    public function getJsComponentName(): ?string;

    public function getJsData(): array;
}
