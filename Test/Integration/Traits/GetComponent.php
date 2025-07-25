<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Util\Controller\LayoutLoader;

trait GetComponent
{
    protected function getComponent(string $blockId): ComponentInterface
    {
        $componentRegistry = ObjectManager::getInstance()->get(ComponentRegistry::class);
        $component = $componentRegistry->getComponentByName($blockId);
        $component->getLocalMessageRegistry()->clearMessages();

        return $component;
    }

    protected function getComponentWithBlock(string $blockId, array $handles = []): ComponentInterface
    {
        $layout = $this->getLayout($handles);
        $block = $layout->getBlock($blockId);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'No block "' . $blockId . '" in layout');

        return $this->getComponent($blockId);
    }

    private function getLayout(array $handles): LayoutInterface
    {
        static $layout;
        if ($layout instanceof LayoutInterface) {
            return $layout;
        }

        $objectManager = ObjectManager::getInstance();
        $layoutLoader = $objectManager->get(LayoutLoader::class);
        return $layoutLoader->load($handles);
    }
}
