<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;
use RuntimeException;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\Hydrator;

class AddComponent implements ObserverInterface
{
    public function __construct(
        private ComponentRegistry $componentRegistry,
        private Hydrator $hydrator
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        try {
            $componentDefinition = $this->componentRegistry->getComponentDefinitionFromBlock($block);
        } catch (RuntimeException $exception) {
            return;
        }

        $component = $componentDefinition->getViewModel();
        if (false === $component instanceof ComponentInterface) {
            return;
        }

        $this->hydrator->hydrate($block, $component);
    }
}
