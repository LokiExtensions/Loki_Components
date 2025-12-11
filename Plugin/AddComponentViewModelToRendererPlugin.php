<?php declare(strict_types=1);

namespace Loki\Components\Plugin;

use Loki\Base\ViewModel\Block\AbstractRenderer;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\View\Element\AbstractBlock;

class AddComponentViewModelToRendererPlugin
{
    public function afterPopulateBlock(
        AbstractRenderer $renderer,
        $return,
        AbstractBlock $block
    ) {
        $viewModel = $renderer->getAncestorBlock()->getViewModel();
        if ($viewModel instanceof ComponentViewModelInterface) {
            $block->setViewModel($viewModel);
        }

        return $return;
    }
}
