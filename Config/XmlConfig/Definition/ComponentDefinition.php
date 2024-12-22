<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

class ComponentDefinition
{
    public function __construct(
        private string $name,
        private string $viewModel = '',
        private string $mutator = '',
        private string $sourceBlock = '',
        private array $targetBlocks = [],
        private array $validators = [],
        private array $filters = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getViewModel(): ?string
    {
        if (!empty($this->viewModel)) {
            return $this->viewModel;
        }

        return null;
    }

    public function getMutator(): ?string
    {
        if (!empty($this->mutator)) {
            return $this->mutator;
        }

        return null;
    }

    public function getSourceBlock(): string
    {
        return $this->sourceBlock;
    }

    public function getTargetBlocks(): array
    {
        return $this->targetBlocks;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
