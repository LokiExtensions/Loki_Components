<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistryFactory;
use Loki\Components\Validator\Validator;

class Component implements ComponentInterface
{
    protected ?ComponentViewModelInterface $viewModel = null;
    protected ?ComponentRepositoryInterface $repository = null;
    protected ?AbstractBlock $block = null;
    protected ?LocalMessageRegistry $localMessageRegistry = null;
    protected bool $isValidated = false;

    public function __construct(
        protected ObjectManagerInterface $objectManager,
        protected LayoutInterface $layout,
        protected ComponentContextInterface $context,
        protected GlobalMessageRegistry $globalMessageRegistry,
        LocalMessageRegistryFactory $localMessageRegistryFactory,
        protected Validator $validator,
        protected Filter $filter,
        protected string $name,
        protected array $targets = [],
        protected array $validators = [],
        protected array $filters = [],
        protected ?string $repositoryClass = null,
        protected ?string $viewModelClass = null,
    ) {
        $this->localMessageRegistry = $localMessageRegistryFactory->create();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }

    public function getTargetString(): string
    {
        return preg_replace('/([^a-z0-9\-\ ]+)/', '-', implode(' ', $this->getTargets()));
    }

    public function hasViewModel(): bool
    {
        return $this->getViewModel() instanceof ComponentViewModelInterface;
    }

    public function getViewModel(): ComponentViewModelInterface
    {
        if ($this->viewModel instanceof ComponentViewModelInterface) {
            return $this->viewModel;
        }

        $this->viewModel = $this->objectManager->create($this->viewModelClass);
        $this->viewModel->setComponent($this);

        $block = $this->getBlock();
        if ($block instanceof AbstractBlock) {
            $this->viewModel->setBlock($block);
        }

        $this->viewModel->setValidator($this->validator);
        $this->viewModel->setFilter($this->filter);

        return $this->viewModel;
    }

    public function hasRepository(): bool
    {
        return $this->getRepository() instanceof ComponentRepositoryInterface;
    }

    public function getRepository(): ?ComponentRepositoryInterface
    {
        if ($this->repository instanceof ComponentRepositoryInterface) {
            return $this->repository;
        }

        if ($this->repositoryClass === null || $this->repositoryClass === '' || $this->repositoryClass === '0') {
            return null;
        }

        $this->repository = $this->objectManager->create($this->repositoryClass);
        $this->repository->setComponent($this);

        return $this->repository;
    }

    public function getValidators(array $validators = []): array
    {
        return array_values(
            array_unique(
                array_merge(
                    $this->validators,
                    $validators,
                    $this->getViewModel()->getValidators(),
                    (array)$this->block?->getValidators(),
                )
            )
        );
    }

    public function getFilters(array $filters = []): array
    {
        return array_values(
            array_unique(
                array_merge(
                    $this->filters,
                    $filters,
                    ['security'],
                    (
                        (array)$this->block?->getFilters()
                    )
                )
            )
        );
    }

    public function getContext(): ComponentContextInterface
    {
        return $this->context;
    }

    public function getBlock(): ?AbstractBlock
    {
        if ($this->block instanceof AbstractBlock) {
            return $this->block;
        }

        $block = $this->layout->getBlock($this->getName());
        if (false === $block instanceof AbstractBlock) {
            return null;
        }

        $this->block = $block;

        return $this->block;
    }

    public function getGlobalMessageRegistry(): GlobalMessageRegistry
    {
        return $this->globalMessageRegistry;
    }

    public function getLocalMessageRegistry(): LocalMessageRegistry
    {
        return $this->localMessageRegistry;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }
}
