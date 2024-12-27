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
use Yireo\LokiComponents\Component\ComponentViewModelInterface;
use Yireo\LokiComponents\Exception\NoComponentFoundException;

class AddHtmlAttributesToComponentBlock implements ObserverInterface
{
    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly JsDataProvider    $jsDataProvider
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        $transport = $observer->getEvent()->getTransport();
        $html = $transport->getHtml();
        if (empty($html)) {
            return;
        }

        if (false === preg_match('/^<([a-z]{2,})/', $html, $match)) {
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

        $htmlAttributes = $this->getHtmlAttributes($component);
        if (empty($htmlAttributes)) {
            return;
        }

        $html = preg_replace('/^<([a-z]{2,})/', '<\1 ' . $htmlAttributes, $html);

        $transport->setHtml($html);
    }

    private function getHtmlAttributes(ComponentInterface $component): string
    {
        $block = $component->getBlock();

        $attributes = (array)$block->getData('html_attributes');
        $attributes['id'] = $this->getElementId($block); // @todo: Double-check that ID has not been added yet
        $attributes['x-data'] = $this->jsDataProvider->getComponentName($component);
        $attributes['x-title'] = $this->jsDataProvider->getComponentId($component);
        $attributes['x-target'] = true; // @todo: Is this needed?

        $htmlAttribute = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            $htmlAttribute .= ' ' . $attributeName . '="' . $attributeValue . '"';
        }

        $htmlAttribute .= " x-init-data='" . $this->getJsData($component) . "'";

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
