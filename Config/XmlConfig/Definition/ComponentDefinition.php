<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

class ComponentDefinition
{
    public function __construct(
        private string $name,
        private string $className,
        private string $context = '',
        private string $viewModel = '',
        private string $repository = '',
        private array $sources = [],
        private array $targets = [],
        private array $validators = [],
        private array $filters = [],
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
        if (!empty($this->context)) {
            return $this->context;
        }

        return null;
    }
    public function getViewModel(): ?string
    {
        if (!empty($this->viewModel)) {
            return $this->viewModel;
        }

        return null;
    }

    public function getRepository(): ?string
    {
        if (!empty($this->repository)) {
            return $this->repository;
        }

        return null;
    }

    public function getSources(): array
    {
        return $this->sources;
    }

    public function getTargets(): array
    {
        return array_unique(array_merge($this->sources, $this->targets));
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
