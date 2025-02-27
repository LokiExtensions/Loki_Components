<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;

abstract class AbstractRenderer implements ArgumentInterface
{
    private array $counters = [];

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
        $nameInLayout = $block->getNameInLayout();
        if (isset($this->counters[$nameInLayout])) {
            $this->counters[$nameInLayout]++;
        } else {
            $this->counters[$nameInLayout] = 0;
        }

        return $this->counters[$nameInLayout];
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
            return 'block'.$this->getCounter($ancestorBlock);
        }

        return '';
    }

    protected function setNameInLayout(AbstractBlock $block, AbstractBlock $ancestorBlock): void
    {
        $alias = $this->getBlockAlias($block, $ancestorBlock);
        $block->setNameInLayout($ancestorBlock->getNameInLayout().'.'.$alias);
    }

    protected function getUniqId(AbstractBlock $block, AbstractBlock $ancestorBlock): string
    {
        $ancestorId = $ancestorBlock->getNameInLayout();
        $blockParts = explode('.', $block->getNameInLayout());
        return $ancestorId.'-'.array_pop($blockParts);
    }
}
