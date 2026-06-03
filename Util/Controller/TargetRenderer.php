<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Loki\Components\Util\IdConvertor;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface as PageLayoutConfigBuilder;

class TargetRenderer
{
    public function __construct(
        private readonly EventManager $eventManager,
        private readonly IdConvertor $idConvertor,
        private readonly LayoutHandlerComposite $layoutHandlerComposite,
        private readonly LayoutLoader $layoutLoader,
        private readonly PageLayoutConfigBuilder $pageLayoutConfigBuilder,
    ) {
    }

    public function render(LayoutInterface $originalLayout, array $targetNames, bool $isolated = false): array
    {
        $originalUpdate = $originalLayout->getUpdate();
        $originalHandles = $originalUpdate->getHandles();
        $pageHandles = $this->extractPageHandles($originalUpdate, $originalHandles);
        $handles = $this->layoutHandlerComposite->getHandles(
            array_values(array_diff($originalHandles, $pageHandles))
        );
        $newLayout = $this->layoutLoader->load($handles, $pageHandles, $isolated);

        return $this->renderBlocks($newLayout, $this->getTargetBlockNames($newLayout, $targetNames));
    }

    private function extractPageHandles($update, array $originalHandles): array
    {
        $pageHandles = [];
        if (method_exists($update, 'getPageHandles')) {
            $pageHandles = (array)$update->getPageHandles();
        }
        if (method_exists($update, 'getPageLayout')) {
            $pageLayout = $update->getPageLayout();
            if ($pageLayout) {
                $pageHandles[] = $pageLayout;
            }
        }

        // Detect page_layout names that were promoted into the handles list by LayoutLoader
        $pageLayoutsConfig = $this->pageLayoutConfigBuilder->getPageLayoutsConfig();
        foreach ($originalHandles as $handle) {
            if ($pageLayoutsConfig->hasPageLayout($handle)) {
                $pageHandles[] = $handle;
            }
        }

        return array_values(array_unique(array_filter($pageHandles)));
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
