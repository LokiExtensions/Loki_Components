<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;
use Yireo\LokiComponents\Component\Component;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\ComponentHydrator;
use Yireo\LokiComponents\Exception\NoComponentFoundException;

class AddComponent implements ObserverInterface
{
    public function __construct(
        private ComponentRegistry $componentRegistry,
        private ComponentHydrator $componentHydrator
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }


        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return;
        }

        if (false === $component instanceof Component) {
            return;
        }

        $this->componentHydrator->hydrateBlock($block, $component);
    }
}
