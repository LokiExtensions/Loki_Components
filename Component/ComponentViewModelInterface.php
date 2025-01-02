<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

interface ComponentViewModelInterface extends ArgumentInterface
{
    public function getElementId(): string;

    public function getBlock(): AbstractBlock;

    public function getValue(): mixed;

    public function getTargets(): array;

    public function getTargetString(): string;

    public function getValidators(): array;

    public function getFilters(): array;

    public function getMessages(): array;

    public function getContext(): ComponentContextInterface;

    public function getTemplate(): ?string;

    public function getComponentName(): string;

    public function getJsComponentName(): ?string;

    public function getJsData(): ?array;
}
