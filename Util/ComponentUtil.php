<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ComponentUtil implements ArgumentInterface
{
    public function __construct(
        private UrlFactory $urlFactory,
        private RequestInterface $request,
    ) {
    }

    public function getElementIdByBlock(AbstractBlock $block): string
    {
        return $this->getElementIdByBlockName($block->getNameInLayout());
    }

    public function getElementIdByBlockName(string $blockName): string
    {
        return preg_replace('/([^a-z0-9\-]+)/', '-', $blockName);
    }

    public function getHandles(AbstractBlock $block): string
    {
        $handles = $block->getLayout()->getUpdate()->getHandles();

        return implode(' ', $handles);
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
