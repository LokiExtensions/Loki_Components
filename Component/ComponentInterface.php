<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistry;

interface ComponentInterface
{
    public function getName(): string;

    public function getContext(): ComponentContextInterface;

    public function getViewModel(): ?ComponentViewModelInterface;

    public function hasViewModel(): bool;

    public function getRepository(): ?ComponentRepositoryInterface;

    public function hasRepository(): bool;

    public function getBlock(): ?AbstractBlock;

    public function getTargets(): array;

    public function getTargetString(): string;

    public function getValidators(array $validators = []): array;

    public function getFilters(array $filters = []): array;

    public function getGlobalMessageRegistry(): GlobalMessageRegistry;

    public function getLocalMessageRegistry(): LocalMessageRegistry;
}
