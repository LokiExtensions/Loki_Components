<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use InvalidArgumentException;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;

class ChildRenderer extends AbstractRenderer
{
    public function all(
        AbstractBlock $parentBlock
    ): string {
        $html = '';
        $childNames = $parentBlock->getChildNames();
        $children = [];

        foreach ($childNames as $childName) {
            $childBlock = $parentBlock->getLayout()->getBlock($childName);
            if (false === $childBlock instanceof AbstractBlock) {
                if ($this->isDeveloperMode()) {
                    $html .= '<!-- WARNING: No child found "' . $childName
                        . '" -->';
                }

                continue;
            }

            $children[] = $childBlock;
        }

        $sortedChildren = $this->sortBlocks($children);

        foreach ($sortedChildren as $sortedChild) {
            $html .= $sortedChild->toHtml();
        }

        return $html;
    }

    public function get(
        AbstractBlock $ancestorBlock,
        string $blockAlias,
        array $data = [],
    ): AbstractBlock {
        $this->ancestorBlock = $ancestorBlock;
        $block = $this->ancestorBlock->getChildBlock($blockAlias);

        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException(
                (string)__(
                    'No child alias "%1" for parent "%2"',
                    $blockAlias,
                    $this->ancestorBlock->getNameInLayout()
                )
            );
        }

        $block->setAlias($blockAlias);
        $this->setNameInLayout($block);
        $this->populateBlock($block, $data);

        return $block;
    }

    public function html(
        AbstractBlock $ancestorBlock,
        string $blockAlias,
        array $data = []
    ) {
        try {
            return (string)$this->get($ancestorBlock, $blockAlias, $data)
                ->toHtml();
        } catch (RuntimeException|InvalidArgumentException $e) {
            if ($this->isDeveloperMode()) {
                return '<!-- WARNING: ' . $e->getMessage() . ' -->';
            }

            return '';
        }
    }

    private function sortBlocks(array $blocks): array
    {
        usort($blocks, function (AbstractBlock $blockA, AbstractBlock $blockB) {
            return (int)$blockA->getSortOrder() <=> (int)$blockB->getSortOrder();
        });

        return $blocks;
    }
}
