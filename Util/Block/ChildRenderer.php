<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use InvalidArgumentException;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;

class ChildRenderer extends AbstractRenderer
{
    public function get(
        string $blockAlias,
        array $data = [],
    ): AbstractBlock {
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

        $this->populateBlock($block, $data);
        $this->setNameInLayout($block);

        return $block;
    }

    public function html(
        string $blockAlias,
        array $data = []
    ) {
        try {
            return (string)$this->get($blockAlias, $data)
                ->toHtml();
        } catch (RuntimeException|InvalidArgumentException $e) {
            if ($this->isDeveloperMode()) {
                return '<!-- ' . $e->getMessage() . ' -->';
            }

            return '';
        }
    }
}
