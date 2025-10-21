<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Loki\Components\Util\Ajax;
use Loki\Components\Util\AppMode;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Util\Component\JsDataProvider;

class AddHtmlAttributesToComponentBlock implements ObserverInterface
{
    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly JsDataProvider $jsDataProvider,
        private readonly Ajax $ajax,
        private readonly AppMode $appMode,
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        if ($block->getNotRendered() === true) {
            return;
        }

        $transport = $observer->getEvent()->getTransport();
        $html = trim((string)$transport->getHtml());
        if (empty($html)) {
            return;
        }

        if (!preg_match('/^<([a-z]{2,})/', $html, $match)) {
            return;
        }

        if (empty($match[1])) {
            return;
        }

        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return;
        }

        $htmlAttributes = $this->getHtmlAttributes($component, $html);
        if (false === strlen($htmlAttributes) > 0) {
            return;
        }

        $html = preg_replace('/^<([a-z]{2,})/', '<\1 ' . $htmlAttributes, $html);

        $initialDataElement = $this->getInitialDataElement($component);

        $html = preg_replace('/^<([^>]+)>/', '<\1>[X-LOKI-INIT-BLOCK]', $html);
        $html = str_replace('[X-LOKI-INIT-BLOCK]', $initialDataElement, $html);

        $transport->setHtml($html);
    }

    private function getInitialDataElement(ComponentInterface $component): string
    {
        $json = $this->getJsData($component);
        return <<<EOF
<script x-ref="initialData" type="text/x-loki-init">$json</script>
EOF;
    }

    private function getHtmlAttributes(ComponentInterface $component, string $currentHtml): string
    {
        $block = $component->getBlock();

        if (!preg_match('/^<([^>]+)>/', $currentHtml, $match)) {
            return $currentHtml;
        }

        $firstTagHtml = $currentHtml[0];

        $attributes = (array)$block->getData('html_attributes');
        $attributes['id'] = $this->getElementId($block);
        $attributes['x-data'] = $this->jsDataProvider->getComponentName($component);

        if ($this->appMode->isDeveloperMode()) {
            $attributes['x-title'] = $this->jsDataProvider->getComponentId($component);
        }

        $htmlAttribute = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            if (str_contains($firstTagHtml, ' ' . $attributeName . '="')) {
                continue;
            }

            $htmlAttribute .= ' ' . $attributeName . '="' . $attributeValue . '"';
        }

        return trim($htmlAttribute);
    }

    private function getJsData(ComponentInterface $component): string
    {
        $componentData = $this->jsDataProvider->getComponentData($component);

        if ($this->ajax->isAjax()) {
            $componentData['ajax'] = true;
        }

        return json_encode($componentData);
    }

    private function getElementId(AbstractBlock $block): string
    {
        $uniqId = (string)$block->getUniqId();
        if (strlen($uniqId) > 0) {
            return $uniqId;
        }

        $nameInLayout = strtolower((string)$block->getNameInLayout());
        return preg_replace('#([^a-zA-Z0-9]{1})#', '-', $nameInLayout);
    }
}
