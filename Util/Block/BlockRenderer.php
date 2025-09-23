<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use InvalidArgumentException;
use Magento\Framework\View\Element\AbstractBlock;
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
        string $blockName,
        array $data = []
    ): AbstractBlock {
        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('No block found with name "%1"', $blockName));
        }

        $this->populateBlock($block, $data);

        if (empty($block->getTemplate())) {
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
        string $blockName,
        array $data = []
    ): string {
        try {
            return $this->get($blockName, $data)->toHtml();
        } catch (InvalidArgumentException|RuntimeException $e) {
            if ($this->isDeveloperMode()) {
                return '<!-- Block with name "' . $blockName . '": ' . $e->getMessage() . ' -->';
            }

            return '';
        }
    }
}
