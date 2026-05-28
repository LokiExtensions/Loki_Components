<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Loki\Components\Util\IdConvertor;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;

class TargetRenderer
{
    public function __construct(
        private readonly EventManager $eventManager,
        private readonly IdConvertor $idConvertor,
        private readonly LayoutHandlerComposite $layoutHandlerComposite,
        private readonly LayoutLoader $layoutLoader
    ) {
    }

    public function render(LayoutInterface $originalLayout, array $targetNames): array
    {
        $handles = $this->layoutHandlerComposite->getHandles($originalLayout->getUpdate()->getHandles());
        $newLayout = $this->layoutLoader->load($handles);

        return $this->renderBlocks($newLayout, $this->getTargetBlockNames($newLayout, $targetNames));
    }

    private function renderBlocks(LayoutInterface $layout, array $blockNames): array
    {
        $blocks = [];
        foreach ($blockNames as $blockName) {
            $block = $layout->getBlock($blockName);
            if ($block instanceof AbstractBlock) {
                $blocks[] = $block;
            }
        }

        usort($blocks, function (AbstractBlock $a, AbstractBlock $b) {
            return (int)$a->getRenderOrder() <=> (int)$b->getRenderOrder();
        });

        $htmlParts = [];
        foreach ($blocks as $block) {
            $htmlParts[] = $block->toHtml();
        }

        return $htmlParts;
    }

    private function getTargetBlockNames(LayoutInterface $layout, array $targets): array
    {
        $blockNames = $this->convertTargetsToBlockNames($layout, $targets);
        $blockNames = array_unique($blockNames);
        $this->eventManager->dispatch('loki_components_blocks', ['blocks' => $blockNames]);

        return $blockNames;
    }

    private function convertTargetsToBlockNames(LayoutInterface $layout, array $targets): array
    {
        $allBlockNames = array_keys($layout->getAllBlocks());

        $blockNames = [];
        foreach ($targets as $target) {
            if (in_array($target, $allBlockNames, true)) {
                $blockNames[] = $target;
                continue;
            }

            foreach ($allBlockNames as $blockName) {
                $elementId = $this->idConvertor->toElementId($blockName);
                if ($elementId === $target) {
                    $blockNames[] = $blockName;
                }
            }
        }

        return $blockNames;
    }
}
