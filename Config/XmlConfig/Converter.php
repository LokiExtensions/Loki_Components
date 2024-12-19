<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use DOMDocument;
use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        return [
            'components' => $this->getComponents($source),
        ];
    }

    private function getComponents(DOMDocument $source): array
    {
        $components = [];
        $componentElements = $source->getElementsByTagName('component');

        foreach ($componentElements as $componentElement) {
            $name = (string)$componentElement->getAttribute('name');
            $domId = (string)$componentElement->getAttribute('domId');
            $className = (string)$componentElement->getAttribute('className');

            $components[$name] = [
                'name' => $name,
                'domId' => $domId,
                'className' => $className,
            ];
        }

        return $components;
    }
}
