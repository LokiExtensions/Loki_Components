<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

class ComponentDefinition
{
    public function __construct(
        private readonly string $name,
        private readonly string $className,
        private readonly string $context = '',
        private readonly string $viewModel = '',
        private readonly string $repository = '',
        private readonly array $targets = [],
        private readonly array $validators = [],
        private readonly array $filters = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getContext(): ?string
    {
        if ($this->context !== '' && $this->context !== '0') {
            return $this->context;
        }

        return null;
    }
    public function getViewModelClass(): ?string
    {
        if ($this->viewModel !== '' && $this->viewModel !== '0') {
            return $this->viewModel;
        }

        return null;
    }

    public function getRepositoryClass(): ?string
    {
        if ($this->repository !== '' && $this->repository !== '0') {
            return $this->repository;
        }

        return null;
    }
    public function getTargets(): array
    {
        return $this->targets;
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
