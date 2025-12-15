<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Util\IdConvertor;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;

class TargetRenderer
{
    public function __construct(
        private readonly EventManager $eventManager,
        private readonly LayoutInterface $layout,
        private readonly IdConvertor $idConvertor,
    ) {
    }

    public function render(LayoutInterface $layout, array $targetNames): array
    {
        return $this->renderBlocks($layout, $this->getTargetBlockNames($targetNames));
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

    private function getTargetBlockNames(array $targets): array
    {
        $blockNames = $this->convertTargetsToBlockNames($targets);
        $blockNames = array_unique($blockNames);
        $this->eventManager->dispatch('loki_components_blocks', ['blocks' => $blockNames]);

        return $blockNames;
    }

    private function convertTargetsToBlockNames(array $targets): array
    {
        $allBlockNames = array_keys($this->layout->getAllBlocks());

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
