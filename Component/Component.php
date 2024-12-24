<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;

class Component implements ComponentInterface
{
    protected ?ComponentViewModelInterface $viewModel = null;
    protected ?ComponentRepositoryInterface $repository = null;
    protected ?AbstractBlock $block = null;

    public function __construct(
        protected ObjectManagerInterface $objectManager,
        protected LayoutInterface $layout,
        protected ComponentContextInterface $context,
        protected string $name,
        protected array $targets = [],
        protected array $validators = [],
        protected array $filters = [],
        protected ?string $repositoryClass = null,
        protected ?string $viewModelClass = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTargets(): array
    {
        return array_unique(array_merge([$this->getName()], $this->targets));
    }

    public function getTargetString(): string
    {
        return preg_replace('/([^a-z0-9\-\ ]+)/', '-', implode(' ', $this->getTargets()));
    }

    public function getViewModel(): ?ComponentViewModelInterface
    {
        if (empty($this->viewModelClass)) {
            return null;
        }

        $this->viewModel = $this->objectManager->create($this->viewModelClass, [
            'component' => $this,
            'block' => $this->getBlock(),
        ]);

        return $this->viewModel;
    }

    public function getRepository(): ?ComponentRepositoryInterface
    {
        $this->repository = $this->objectManager->create($this->repositoryClass, [
            'component' => $this,
        ]);

        return $this->repository;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getContext(): ComponentContextInterface
    {
        return $this->context;
    }

    public function getBlock(): AbstractBlock
    {
        if ($this->block instanceof AbstractBlock) {
            return $this->block;
        }

        $this->block = $this->layout->getBlock($this->getName());;
        return $this->block;
    }
}
