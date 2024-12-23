<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Messages\MessageManager;
use Yireo\LokiComponents\Util\ComponentUtil;

abstract class ComponentViewModel implements ComponentViewModelInterface
{
    public function __construct(
        protected ComponentInterface $component,
        protected MessageManager $messageManager,
        protected AbstractBlock $block,
    ) {
    }

    public function getData(): mixed
    {
        return $this->getComponent()->getRepository()->get();
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }
    public function getTargetString(): string
    {
        return $this->getComponent()->getTargetString();
    }

    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }

    protected function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    protected function getContext(): ComponentContextInterface
    {
        return $this->getComponent()->getContext();
    }
}
