<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Loki\Components\Component\ComponentViewModelInterface;

abstract class AbstractRenderer implements ArgumentInterface
{
    public function __construct(
        protected LayoutInterface $layout,
        protected AppState $appState,
        protected ChildCounter $childCounter,
    ) {
    }

    protected function populateBlock(
        AbstractBlock $block,
        AbstractBlock $ancestorBlock,
        array $data = []
    ): void {
        $block->setAncestorBlock($ancestorBlock);
        $block->setUniqId($this->getUniqId($block, $ancestorBlock));
        $block->addData($data);

        $viewModel = $ancestorBlock->getViewModel();
        if ($viewModel instanceof ComponentViewModelInterface) {
            $block->setViewModel($viewModel);
        }
    }

    protected function getCounter(AbstractBlock $block): int
    {
        return $this->childCounter->getCounter($block->getNameInLayout());
    }

    protected function getBlockAlias(
        ?AbstractBlock $block = null,
        ?AbstractBlock $ancestorBlock = null,
        array $data = []
    ): string {
        if ($block instanceof AbstractBlock) {
            $alias = $block->getAlias();
            if (!empty($alias)) {
                return $alias;
            }
        }

        if (isset($data['alias'])) {
            return $data['alias'];
        }

        if ($ancestorBlock instanceof AbstractBlock) {
            return 'block' . $this->getCounter($ancestorBlock);
        }

        return '';
    }

    protected function setNameInLayout(AbstractBlock $block, AbstractBlock $ancestorBlock): void
    {
        $alias = $this->getBlockAlias($block, $ancestorBlock);
        $block->setNameInLayout($ancestorBlock->getNameInLayout() . '.' . $alias);
    }

    protected function getUniqId(AbstractBlock $block, AbstractBlock $ancestorBlock): string
    {
        $ancestorId = $ancestorBlock->getNameInLayout();
        $blockParts = explode('.', $block->getNameInLayout());
        return $ancestorId . '-' . array_pop($blockParts);
    }

    protected function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER;
    }
}
