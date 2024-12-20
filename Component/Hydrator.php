<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;

class Hydrator
{
    public function hydrate(AbstractBlock $block, ComponentInterface $component): void
    {
        $block->setComponent($component);
        $block->assign('component', $component);

        if (method_exists($component, 'setBlock')) {
            $component->setBlock($block);
        }
    }
}
