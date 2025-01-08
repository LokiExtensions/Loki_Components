<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentRegistry;

trait GetComponent
{
    protected function getComponent(string $blockId): ComponentInterface
    {
        $objectManager = Bootstrap::getObjectManager();
        //$objectManager = ObjectManager::getInstance();
        $layout = $objectManager->create(LayoutInterface::class);
        $layout->getUpdate()->load([
            'default',
            'checkout_index_index',
            'loki_checkout',
            'loki_checkout_theme_onestep',
        ]);
        $layout->getUpdate()->addPageHandles(['1column']);
        $layout->generateElements();
        //$layout->addOutputElement($blockId);
        //$layout->getOutput();

        $this->assertFalse($layout->isCacheable());

        $this->assertNotEmpty($layout->getAllBlocks());

        $debug = '';
        $debug .= ' Block: ' . var_export($layout->getAllBlocks(), true). "\n";
        $debug .= ' Handles: ' . var_export($layout->getUpdate()->getHandles(), true). "\n";
        $debug .= ' Containers: ' . var_export($layout->getUpdate()->getContainers(), true). "\n";

        $block = $layout->getBlock($blockId);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'No block "'.$blockId.'" in layout: '.$debug);





        $componentRegistry = $objectManager->get(ComponentRegistry::class);
        $component = $componentRegistry->getComponentByName($blockId);

        $this->assertInstanceOf(AbstractBlock::class, $component->getBlock());
        return $component;
    }
}
