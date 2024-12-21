<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Mutator;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentInterface;

class Hydrator
{
    public function hydrate(
        MutatorInterface $mutator,
        AbstractBlock $block,
        ComponentInterface $component
    ): void {
    }
}
