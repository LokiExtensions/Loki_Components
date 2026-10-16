<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Loki\Components\Util\Ajax;
use Loki\Components\Util\AppMode;
use Loki\Components\Util\Block\GetElementId;
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
    private const FIRST_TAG_PATTERN = '/^<([a-zA-Z][a-zA-Z0-9-]*)(?=[\s\/>])((?:"[^"]*"|\'[^\']*\'|[^\'">])*)>/';

    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly JsDataProvider $jsDataProvider,
        private readonly Ajax $ajax,
        private readonly AppMode $appMode,
        private readonly GetElementId $getElementId
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
        $html = trim((string)$transport->getHtml(), " \f\n\r\t\v\x00");
        if (empty($html)) {
            return;
        }

        if (!preg_match(self::FIRST_TAG_PATTERN, $html, $match)) {
            return;
        }

        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return;
        }

        $tagName = $match[1];
        $existingAttributes = $match[2];
        $selfClosing = '';
        if (str_ends_with(rtrim($existingAttributes), '/')) {
            $existingAttributes = substr(rtrim($existingAttributes), 0, -1);
            $selfClosing = ' /';
        }

        $htmlAttributes = $this->getHtmlAttributes($component, $existingAttributes);
        $newTag = '<' . $tagName . ' ' . $htmlAttributes . $selfClosing . '>';

        $html = $newTag . $this->getInitialDataElement($component) . substr($html, strlen($match[0]));

        $transport->setHtml($html);
    }

    private function getInitialDataElement(ComponentInterface $component): string
    {
        $json = $this->getJsData($component);
        return <<<EOF
<data hidden x-ref="initialData">$json</data>
EOF;
    }

    private function getHtmlAttributes(ComponentInterface $component, string $existingAttributes): string
    {
        $block = $component->getBlock();

        $attributes = (array)$block->getData('html_attributes');
        $attributes['id'] = $this->getElementId->execute($block);
        $attributes['x-data'] = $this->jsDataProvider->getComponentName($component);

        if ($this->appMode->isDeveloperMode()) {
            $attributes['x-title'] = $this->jsDataProvider->getComponentId($component);
        }

        $existingAttributeNames = $this->getAttributeNames($existingAttributes);

        $htmlAttributes = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeName = $this->convertAlpineShorthand((string)$attributeName);
            if (in_array(strtolower($attributeName), $existingAttributeNames, true)) {
                continue;
            }

            $htmlAttributes[] = $attributeName . '="' . $this->escapeAttributeValue($attributeValue) . '"';
        }

        $allAttributes = $this->convertAlpineShorthands($existingAttributes) . ' ' . implode(' ', $htmlAttributes);

        return trim($allAttributes, " \f\n\r\t\v\x00");
    }

    private function getAttributeNames(string $attributes): array
    {
        $attributes = $this->convertAlpineShorthands($attributes);
        $attributes = (string)preg_replace('/"[^"]*"|\'[^\']*\'/', '""', $attributes);
        preg_match_all('/(?:^|\s)([^\s=\/>"\']+)/', $attributes, $matches);

        return array_map('strtolower', $matches[1]);
    }

    private function convertAlpineShorthands(string $attributes): string
    {
        return (string)preg_replace_callback(
            '/("[^"]*"|\'[^\']*\')|(?<=^|\s)@([^\s=\/>"\']+)/',
            fn (array $match) => $match[1] !== '' ? $match[1] : 'x-on:' . $match[2],
            $attributes
        );
    }

    private function convertAlpineShorthand(string $attributeName): string
    {
        if (str_starts_with($attributeName, '@')) {
            return 'x-on:' . substr($attributeName, 1);
        }

        return $attributeName;
    }

    private function escapeAttributeValue(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
    }

    private function getJsData(ComponentInterface $component): string
    {
        $componentData = $this->jsDataProvider->getComponentData($component);

        if ($this->ajax->isAjax()) {
            $componentData['ajax'] = true;
        }

        return json_encode($componentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    }
}
