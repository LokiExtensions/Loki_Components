<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use DOMDocument;
use DOMNode;
use Magento\Framework\Config\ConverterInterface;
use Yireo\LokiComponents\Component\Component;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

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

    /**
     * @param DOMDocument $source
     * @return ComponentDefinition[]
     */
    private function getComponentDefinitions(DOMDocument $source): array
    {
        $componentElements = $source->getElementsByTagName('component');
        $componentClass = Component::class;

        foreach ($componentElements as $componentElement) {
            $name = (string)$componentElement->getAttribute('name');
            $context = (string)$componentElement->getAttribute('context');
            $viewModel = (string)$componentElement->getAttribute('viewModel');
            $repository = (string)$componentElement->getAttribute('repository');

            $componentDefinitions[$name] = [
                'class' => $componentClass,
                'name' => $name,
                'context' => $context,
                'viewModel' => $viewModel,
                'repository' => $repository,
                'sources' => $this->getSources($componentElement),
                'targets' => $this->getTargets($componentElement),
                'validators' => $this->getValidators($componentElement),
                'filters' => $this->getFilters($componentElement),
            ];
        }

        return $componentDefinitions;
    }

    private function getSources(DOMNode $componentElement): array
    {
        $sources = [];
        $sourceElements = $componentElement->getElementsByTagName('source');
        foreach ($sourceElements as $sourceElement) {
            $sources[] = (string)$sourceElement->getAttribute('name');
        }

        return $sources;
    }

    private function getTargets(DOMNode $componentElement): array
    {
        $targets = [];
        $targetElements = $componentElement->getElementsByTagName('target');
        foreach ($targetElements as $targetElement) {
            $targets[] = (string)$targetElement->getAttribute('name');
        }

        return $targets;
    }

    private function getValidators(DOMNode $componentElement): array
    {
        $validators = [];
        $validatorElements = $componentElement->getElementsByTagName('validator');
        foreach ($validatorElements as $validatorElement) {
            $disabled = (bool)$validatorElement->getAttribute('disabled');
            if ($disabled) {
                continue;
            }

            $validators[] = (string)$validatorElement->getAttribute('name');
        }

        return $validators;
    }

    private function getFilters(DOMNode $componentElement): array
    {
        $filters = [];
        $filterElements = $componentElement->getElementsByTagName('filter');
        foreach ($filterElements as $filterElement) {
            $disabled = (bool)$filterElement->getAttribute('disabled');
            if ($disabled) {
                continue;
            }

            $filters[] = (string)$filterElement->getAttribute('name');
        }

        return $filters;
    }
}
