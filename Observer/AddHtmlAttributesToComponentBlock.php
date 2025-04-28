<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Util\Component\JsDataProvider;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Exception\NoComponentFoundException;

class AddHtmlAttributesToComponentBlock implements ObserverInterface
{
    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly JsDataProvider $jsDataProvider
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
        $html = $transport->getHtml();
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
        if ($htmlAttributes === '' || $htmlAttributes === '0') {
            return;
        }

        $html = preg_replace('/^<([a-z]{2,})/', '<\1 '.$htmlAttributes, $html);

        $initialDataElement = $this->getInitialDataElement($component);
        $html = preg_replace('/^<([^>]+)>/', '<\1>'.$initialDataElement, $html);

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
        $attributes['x-title'] = $this->jsDataProvider->getComponentId($component);

        $htmlAttribute = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            if (str_contains($firstTagHtml, ' '.$attributeName.'="')) {
                continue;
            }

            $htmlAttribute .= ' '.$attributeName.'="'.$attributeValue.'"';
        }

        return trim($htmlAttribute);
    }

    private function getJsData(ComponentInterface $component): string
    {
        $componentData = $this->jsDataProvider->getComponentData($component);

        return json_encode($componentData);
    }

    private function getElementId(AbstractBlock $block): string
    {
        $nameInLayout = strtolower((string)$block->getNameInLayout());

        // @todo: Find all instances of this kind of thing and DRY
        return preg_replace('#([^a-zA-Z0-9]{1})#', '-', $nameInLayout);
    }
}
