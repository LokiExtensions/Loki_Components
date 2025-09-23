<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use InvalidArgumentException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use RuntimeException;

class BlockRenderer extends AbstractRenderer
{
    /**
     * @param string $blockName
     * @param array $data
     *
     * @return AbstractBlock
     */
    public function get(
        AbstractBlock $ancestorBlock,
        string $blockName,
        array $data = []
    ): AbstractBlock {
        $this->ancestorBlock = $ancestorBlock;
        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('No block found with name "%1"', $blockName));
        }

        $this->populateBlock($block, $data);

        if ($block instanceof Template && false === strlen($block->getTemplate()) > 0) {
            throw new RuntimeException((string)__('No template found with block "%1"', $blockName));
        }

        return $block;
    }

    /**
     * @param string $blockName
     * @param array $data
     * @return string
     */
    public function html(
        AbstractBlock $ancestorBlock,
        string $blockName,
        array $data = []
    ): string {
        try {
            return $this->get($ancestorBlock, $blockName, $data)->toHtml();
        } catch (InvalidArgumentException|RuntimeException $e) {
            if ($this->isDeveloperMode()) {
                return '<!-- WARNING: Block with name "' . $blockName . '": ' . $e->getMessage() . ' -->';
            }

            return '';
        }
    }
}
