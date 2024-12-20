<?php declare(strict_types=1);

namespace Yireo\LokiComponents\ViewModel;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\LokiComponents\Component\ComponentRegistry;

class ComponentUtil implements ArgumentInterface
{
    public function __construct(
        private UrlFactory $urlFactory,
        private RequestInterface $request,
        private ComponentRegistry $componentRegistry
    ) {
    }
    public function getElementIdByBlock(AbstractBlock $block): string
    {
        return $this->getElementIdByBlockName($block->getNameInLayout());
    }

    public function getElementIdByBlockName(string $blockName): string
    {
        return $this->componentRegistry->getElementIdByBlockName($blockName);
    }
    public function getHandles(AbstractBlock $block): string
    {
        $handles = $block->getLayout()->getUpdate()->getHandles();

        return implode(' ', $handles);
    }

    public function getTargets(AbstractBlock $block): string
    {
        $targetNames = [];
        $targets = (array)$block->getTargets();
        foreach ($targets as $target) {
            $targetNames[] = $target;
        }

        $componentDefinition = $this->componentRegistry->getComponentDefinitionFromBlockName($block->getNameInLayout());
        foreach ($componentDefinition->getBlockDefinitions() as $blockDefinition) {
            $targetNames[] = $blockDefinition->getElementId();
        }

        $targetNames = array_unique($targetNames);
        return implode(' ', $targetNames);
    }

    public function isAjax(): bool
    {
        /** @var Http $request */
        $request = $this->request;

        return $request->getHeader('X-Alpine-Request') === 'true';
    }

    public function getPostUrl(): string
    {
        return $this->urlFactory->create()->getUrl('loki_components/index/html');
    }

    public function getFormHtml(AbstractBlock $block): string
    {
        return (string)$block->getLayout()->getBlock('loki-components.form_html')
            ->setData('source_block', $block)
            ->toHtml();
    }
}
