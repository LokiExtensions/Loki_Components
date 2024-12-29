<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Messages\MessageManager;
use Yireo\LokiComponents\Messages\MessageManagerFactory;

abstract class ComponentViewModel implements ComponentViewModelInterface
{
    protected ?MessageManager $messageManager = null;

    public function __construct(
        protected ComponentInterface $component,
        protected MessageManagerFactory $messageManagerFactory,
        protected ?AbstractBlock $block = null,
    ) {
    }

    public function getElementId(): string
    {
        return preg_replace('/([^a-zA-Z0-9\-]+)/', '-', $this->block->getNameInLayout());
    }

    public function getValue(): mixed
    {
        if (false === $this->getComponent()->hasRepository()) {
            return null;
        }

        return $this->getComponent()->getRepository()->get();
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function getTargets(): array
    {
        return $this->getComponent()->getTargets();
    }

    public function getTargetString(): string
    {
        return $this->getComponent()->getTargetString();
    }

    public function getValidators(): array
    {
        return $this->getComponent()->getValidators();
    }

    public function getFilters(): array
    {
        return $this->getComponent()->getFilters();
    }

    public function getMessageManager(): MessageManager
    {
        if (false === $this->messageManager instanceof MessageManager) {
            //echo "Create message manager for ".$this->component->getName();
            $this->messageManager = $this->messageManagerFactory->create();
        }

        return $this->messageManager;
    }

    public function getContext(): ComponentContextInterface
    {
        return $this->component->getContext();
    }

    protected function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    protected function getRepository(): ?ComponentRepositoryInterface
    {
        return $this->component->getRepository();
    }

    public function getTemplate(): ?string
    {
        return null;
    }

    public function getJsComponentName(): ?string
    {
        return null;
    }

    public function getJsData(): ?array
    {
        return null;
    }
}
