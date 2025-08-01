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
     * @param AbstractBlock $ancestorBlock
     * @param array $data
     *
     * @return Template
     */
    public function get(
        AbstractBlock $ancestorBlock,
        string $blockName,
        array $data = []
    ): AbstractBlock {
        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('No block found with name "%1"', $blockName));
        }

        $this->populateBlock($block, $ancestorBlock, $data);

        if (empty($block->getTemplate())) {
            throw new RuntimeException((string)__('No template found with block "%1"', $blockName));
        }

        return $block;
    }

    /**
     * @param string $blockName
     * @param AbstractBlock $ancestorBlock
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
                return '<!-- Block with name "' . $blockName . '": ' . $e->getMessage() . ' -->';
            }

            return '';
        }
    }
}
