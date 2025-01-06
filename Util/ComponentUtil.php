<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey as FormKeyModel;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ComponentUtil implements ArgumentInterface
{
    public function __construct(
        private UrlFactory $urlFactory,
        private RequestInterface $request,
        private FormKeyModel $formKey
    ) {
    }

    public function getElementIdByBlock(AbstractBlock $block): string
    {
        return $this->getElementIdByBlockName($block->getNameInLayout());
    }

    public function convertToElementId(string $blockName): string
    {
        return preg_replace('/([^a-z0-9\-]+)/', '-', $blockName);
    }

    /**
     * @deprecated Use convertToElementId() instead
     */
    public function getElementIdByBlockName(string $blockName): string
    {
        return $this->convertToElementId($blockName);
    }

    public function getHandles(AbstractBlock $block): array
    {
        return $block->getLayout()->getUpdate()->getHandles();
    }

    // @todo: Deprecate this in favor of ComponentContext::isAjax()
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

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
