<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use InvalidArgumentException;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;

class ChildRenderer extends AbstractRenderer
{
    public function get(
        AbstractBlock $ancestorBlock,
        string $blockAlias,
        array $data = [],
    ): AbstractBlock {
        $block = $ancestorBlock->getChildBlock($blockAlias);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException(
                (string)__(
                    'No child alias "%1" for parent "%2"',
                    $blockAlias,
                    $ancestorBlock->getNameInLayout()
                )
            );
        }

        $this->populateBlock($block, $ancestorBlock, $data);
        $this->setNameInLayout($block, $ancestorBlock);

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
                return '<!-- ' . $e->getMessage() . ' -->';
            }

            return '';
        }
    }
}
