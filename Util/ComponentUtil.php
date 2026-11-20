<?php declare(strict_types=1);

namespace Loki\Components\Util;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Data\Form\FormKey as FormKeyModel;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Loki\Components\Util\Security\AjaxSignature;

class ComponentUtil implements ArgumentInterface
{
    public function __construct(
        private readonly UrlFactory $urlFactory,
        private readonly RequestInterface $request,
        private readonly FormKeyModel $formKey,
        private readonly IdConvertor $idConvertor,
        private readonly AjaxSignature $ajaxSignature,
        private readonly State $appState
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

    public function getPageHandles(AbstractBlock $block): array
    {
        /** @var \Magento\Framework\View\Model\Layout\Merge $update */
        $update = $block->getLayout()->getUpdate();
        $pageLayout = $update->getPageLayout();
        if (false === is_string($pageLayout) || $pageLayout === '') {
            return [];
        }

        return [$pageLayout];
    }

    public function getRequestData(): array
    {
        /** @var Http $request */
        $request = $this->request;

        return $this->scrub([
            'params' => $request->getParams(),
            'route_name' => $request->getRouteName(),
            'module_name' => $request->getModuleName(),
            'controller_module' => $request->getControllerModule(),
            'controller_name' => $request->getControllerName(),
            'action_name' => $request->getActionName(),
            'request_uri' => $request->getRequestUri(),
        ]);
    }

    /**
     * Replace invalid UTF-8 (like "?foo=%FF") because it can not be JSON encoded
     */
    private function scrub(mixed $value): mixed
    {
        if (is_string($value)) {
            return mb_scrub($value, 'UTF-8');
        }

        if (false === is_array($value)) {
            return $value;
        }

        $scrubbed = [];
        foreach ($value as $key => $subValue) {
            $scrubbed[is_string($key) ? mb_scrub($key, 'UTF-8') : $key] = $this->scrub($subValue);
        }

        return $scrubbed;
    }

    public function getComponentUpdateData(AbstractBlock $block): array
    {
        return [
            'handles' => $this->getHandles($block),
            'pageHandles' => $this->getPageHandles($block),
            'request' => $this->getRequestData(),
        ];
    }

    public function getSignature(AbstractBlock $block): string
    {
        return $this->ajaxSignature->sign(
            $this->getComponentUpdateData($block)
        );
    }

    public function getPostUrl(): string
    {
        return $this->urlFactory->create()->getUrl('loki_components/index/html');
    }

    public function getPollingUrl(): string
    {
        return $this->urlFactory->create()->getUrl('loki_components/index/polling');
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
