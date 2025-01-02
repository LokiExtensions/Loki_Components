<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Yireo\LokiComponents\Messages\GlobalMessageManager;
use Yireo\LokiComponents\Messages\LocalMessageManager;
use Yireo\LokiComponents\Messages\LocalMessageManagerFactory;
use Yireo\LokiComponents\Messages\MessageManager;
use Yireo\LokiComponents\Util\DefaultTargets;

class Component implements ComponentInterface
{
    protected ?ComponentViewModelInterface $viewModel = null;
    protected ?ComponentRepositoryInterface $repository = null;
    protected ?LocalMessageManager $localMessageManager = null;
    protected ?AbstractBlock $block = null;

    public function __construct(
        protected ObjectManagerInterface $objectManager,
        protected LayoutInterface $layout,
        protected ComponentContextInterface $context,
        protected LocalMessageManagerFactory $localMessageManagerFactory,
        protected GlobalMessageManager $globalMessageManager,
        protected DefaultTargets $defaultTargets,
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
        $targets = [];
        if (false === $this->skipSelf()) {
            $targets[] = $this->getName();
        }

        $targets = array_merge($targets, $this->defaultTargets->getTargets());
        $targets = array_merge($targets, $this->targets);

        return array_unique(array_merge($targets));
    }

    public function getTargetString(): string
    {
        return preg_replace('/([^a-z0-9\-\ ]+)/', '-', implode(' ', $this->getTargets()));
    }

    public function hasViewModel(): bool
    {
        return $this->getViewModel() instanceof ComponentViewModelInterface;
    }

    public function getViewModel(): ?ComponentViewModelInterface
    {
        if ($this->viewModel instanceof ComponentViewModelInterface) {
            return $this->viewModel;
        }

        if (empty($this->viewModelClass)) {
            return null;
        }


        $this->viewModel = $this->objectManager->create($this->viewModelClass, [
            'component' => $this,
            'block' => $this->getBlock(),
        ]);

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

        if (empty($this->repositoryClass)) {
            return null;
        }

        $this->repository = $this->objectManager->create($this->repositoryClass, [
            'component' => $this,
        ]);

        return $this->repository;
    }

    public function getValidators(array $validators = []): array
    {
        return array_values(
            array_unique(
                array_merge(
                    $this->validators,
                    $validators,
                    (array)$this->block?->getValidators()
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
                    ((array)$this->block?->getFilters()
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

    public function getGlobalMessageManager(): GlobalMessageManager
    {
        return $this->globalMessageManager;
    }

    public function getLocalMessageManager(): LocalMessageManager
    {
        if (false === $this->localMessageManager instanceof MessageManager) {
            $this->localMessageManager = $this->localMessageManagerFactory->create();
        }

        return $this->localMessageManager;
    }

    // @todo: Rewrite with XML variation
    private function skipSelf(): bool
    {
        if (false === $this->getViewModel() instanceof ComponentViewModelInterface) {
            return false;
        }

        if (false === method_exists($this->getViewModel(), 'skipSelf')) {
            return false;
        }

        return call_user_func([$this->getViewModel(), 'skipSelf']);
    }
}
