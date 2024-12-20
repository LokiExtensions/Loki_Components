<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Mutator;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentInterface;

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
