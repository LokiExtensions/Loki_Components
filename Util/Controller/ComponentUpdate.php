<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Component\Component;
use Magento\Framework\View\Element\AbstractBlock;

class ComponentUpdate
{
    public function __construct(
        private readonly AbstractBlock $block,
        private readonly Component $component,
        private readonly mixed $componentData = null
    ) {
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function getComponentData(): mixed
    {
        return $this->componentData;
    }
}
