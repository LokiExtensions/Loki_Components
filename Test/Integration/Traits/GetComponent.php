<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
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

        $layout = $this->getLayout();
        $block = $layout->getBlock($blockId);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'No block "'.$blockId.'" in layout');

        $componentRegistry = ObjectManager::getInstance()->get(ComponentRegistry::class);
        $component = $componentRegistry->getComponentByName($blockId);
        $components[$blockId] = $component;

        return $component;
    }

    private function getLayout(): LayoutInterface
    {
        static $layout;
        if ($layout instanceof LayoutInterface) {
            return $layout;
        }

        $objectManager = ObjectManager::getInstance();
        $layoutLoader = $objectManager->get(LayoutLoader::class);

        // @todo: Make this configurable
        $layout = $layoutLoader->load([
            'default',
            'checkout_index_index',
            'loki_checkout',
            'loki_checkout_theme_onestep',
        ]);

        return $layout;
    }
}
