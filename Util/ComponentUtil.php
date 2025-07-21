<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey as FormKeyModel;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ComponentUtil implements ArgumentInterface
{
    public function __construct(
        private readonly UrlFactory $urlFactory,
        private readonly RequestInterface $request,
        private readonly FormKeyModel $formKey,
        private readonly IdConvertor $idConvertor
    ) {
    }

    /**
     * @param string $blockName
     * @return string
     * @deprecated Use IdConvertor instead
     */
    public function convertToElementId(string $blockName): string
    {
        return $this->idConvertor->toElementId($blockName);
    }

    /**
     * @deprecated Use convertToElementId() instead
     */
    public function getElementIdByBlockName(string $blockName): string
    {
        return $this->idConvertor->toElementId($blockName);
    }

    public function getHandles(AbstractBlock $block): array
    {
        return $block->getLayout()->getUpdate()->getHandles();
    }

    public function getRequestParams(): array
    {
        return $this->request->getParams();
    }

    public function getCurrentUri(): string
    {
        /** @var Http $request */
        $request = $this->request;
        return $request->getFullActionName();
    }

    public function getPostUrl(): string
    {
        return $this->urlFactory->create()->getUrl('loki_components/index/html');
    }

    public function getFormHtml(AbstractBlock $block): string
    {
        /** @var AbstractBlock $formBlock */
        $formBlock = $block->getLayout()->getBlock('loki-components.form_html');
        return $formBlock
            ->setData('source_block', $block)
            ->toHtml();
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
