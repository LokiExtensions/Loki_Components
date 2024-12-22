<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\ViewModel;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentInterface;

abstract class ViewModel implements ViewModelInterface
{
    protected ?ComponentInterface $component = null;
    protected ?AbstractBlock $block = null;


    public function getComponent(): ?ComponentInterface
    {
        return $this->component;
    }

    public function setComponent(ComponentInterface $component): void
    {
        $this->component = $component;
    }

    public function hasComponent(): bool
    {
        return $this->component instanceof ComponentInterface;
    }

    public function setBlock(AbstractBlock $block)
    {
        $this->block = $block;
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function hasBlock(): bool
    {
        return $this->block instanceof AbstractBlock;
    }
}
