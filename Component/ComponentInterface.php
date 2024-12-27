<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;

interface ComponentInterface
{
    public function getContext(): ComponentContextInterface;

    public function getViewModel(): ?ComponentViewModelInterface;
    public function hasViewModel(): bool;

    public function getRepository(): ?ComponentRepositoryInterface;
    public function hasRepository(): bool;

    public function getBlock(): AbstractBlock;

    public function getTargets(): array;

    public function getTargetString(): string;

    public function getValidators(array $validators = []): array;

    public function getFilters(array $filters = []): array;
}
