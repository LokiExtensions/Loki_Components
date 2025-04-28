<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use RuntimeException;

class TemplateRenderer extends AbstractRenderer
{
    public function get(
        AbstractBlock $ancestorBlock,
        string $templateName,
        array $data = []
    ): Template {
        $blockAlias = $this->getBlockAlias(null, $ancestorBlock, $data);
        $blockName = $ancestorBlock->getNameInLayout().'.'.$blockAlias;
        $block = $this->createBlockFromTemplate($templateName, $blockName);

        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('No block found with template "%1"', $templateName));
        }

        $this->populateBlock($block, $ancestorBlock, $data);

        return $block;
    }

    public function html(
        AbstractBlock $ancestorBlock,
        string $templateName,
        array $data = []
    ): string {
        return $this->get($ancestorBlock, $templateName, $data)->toHtml();
    }

    private function createBlockFromTemplate(string $templateName, string $blockName): Template
    {
        if (false === str_contains($templateName, '::')) {
            $templateName = 'Yireo_LokiComponents::'.$templateName;
        }

        if (false === str_contains($templateName, '.phtml')) {
            $templateName .= '.phtml';
        }

        $block = $this->layout->getBlock($blockName);
        if ($block instanceof Template) {
            return $block;
        }

        return $this->layout->createBlock(Template::class, $blockName)->setTemplate($templateName);
    }
}
