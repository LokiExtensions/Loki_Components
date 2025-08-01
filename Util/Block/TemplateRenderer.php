<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

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
        $blockName = $ancestorBlock->getNameInLayout() . '.' . $blockAlias;
        $block = $this->createBlockFromTemplate($templateName, $blockName);

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
            $templateName = 'Loki_Components::' . $templateName;
        }

        if (false === str_contains($templateName, '.phtml')) {
            $templateName .= '.phtml';
        }

        $block = $this->layout->getBlock($blockName);
        if ($block instanceof Template) {
            return $block;
        }

        /** @var Template $block */
        $block = $this->layout->createBlock(Template::class, $blockName);
        $block->setTemplate($templateName); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock
        return $block;
    }
}
