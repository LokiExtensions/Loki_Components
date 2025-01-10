<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Util\Controller\LayoutLoader;

trait GetComponent
{
    protected function getComponent(string $blockId): ComponentInterface
    {
        static $components = [];
        if (isset($components[$blockId])) {
            $component = $components[$blockId];
            $this->assertInstanceOf(AbstractBlock::class, $component->getBlock());
            $component->getLocalMessageRegistry()->clearMessages();

            return $component;
        }

        $objectManager = ObjectManager::getInstance();
        $layoutLoader = $objectManager->get(LayoutLoader::class);
        $layout = $layoutLoader->load([
            'default',
            'checkout_index_index',
            'loki_checkout',
            'loki_checkout_theme_onestep',
        ]);

        $block = $layout->getBlock($blockId);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'No block "'.$blockId.'" in layout');

        $componentRegistry = $objectManager->get(ComponentRegistry::class);
        $component = $componentRegistry->getComponentByName($blockId);
        $components[$blockId] = $component;

        return $component;
    }
}
