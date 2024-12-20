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
            'components' => $this->getComponentDefinitions($source),
        ];
    }

    private function getComponentDefinitions(DOMDocument $source): array
    {
        $componentDefinitions = [];
        $componentElements = $source->getElementsByTagName('component');

        foreach ($componentElements as $componentElement) {
            $name = (string)$componentElement->getAttribute('name');
            $viewModel = (string)$componentElement->getAttribute('viewModel');
            $mutator = (string)$componentElement->getAttribute('mutator');

            $blockDefinitions = [];
            $blockElements = $componentElement->getElementsByTagName('block');
            foreach ($blockElements as $blockElement) {
                $blockDefinitions[] = [
                    'name' => (string)$blockElement->getAttribute('name'),
                ];
            }

            $componentDefinitions[$name] = [
                'name' => $name,
                'viewModel' => $viewModel,
                'mutator' => $mutator,
                'blocks' => $blockDefinitions,
            ];
        }

        return $componentDefinitions;
    }
}
