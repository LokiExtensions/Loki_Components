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
        $componentDefinitions = [];
        $componentGroupElements = $source->getElementsByTagName('componentGroup');

        foreach ($componentGroupElements as $componentGroupElement) {
            $defaultComponentClass = (string)$componentGroupElement->getAttribute('defaultClass');
            $defaultContext = (string)$componentGroupElement->getAttribute('defaultContext');
            $defaultViewModel = (string)$componentGroupElement->getAttribute('defaultViewModel');
            $defaultRepository = (string)$componentGroupElement->getAttribute('defaultRepository');
            $defaultTargets = $this->getDefaultTargets($componentGroupElement);

            if (empty($defaultComponentClass)) {
                $defaultComponentClass = Component::class;
            }

            $componentElements = $componentGroupElement->getElementsByTagName('component');

            foreach ($componentElements as $componentElement) {
                $name = (string)$componentElement->getAttribute('name');
                $componentClass = (string)$componentElement->getAttribute('class');
                $context = (string)$componentElement->getAttribute('context');
                $viewModel = (string)$componentElement->getAttribute('viewModel');
                $repository = (string)$componentElement->getAttribute('repository');

                $componentDefinitions[$name] = [
                    'name' => $name,
                    'class' => !empty($componentClass) ? $componentClass : $defaultComponentClass,
                    'context' => !empty($context) ? $context : $defaultContext,
                    'viewModel' => !empty($viewModel) ? $viewModel : $defaultViewModel,
                    'repository' => !empty($repository) ? $repository : $defaultRepository,
                    'targets' => array_merge($defaultTargets, $this->getTargets($componentElement)),
                    'validators' => $this->getValidators($componentElement),
                    'filters' => $this->getFilters($componentElement),
                ];
            }
        }

        return $componentDefinitions;
    }

    private function getDefaultTargets(DOMNode $element): array
    {
        $targets = [];
        $targetElements = $element->getElementsByTagName('defaultTarget');
        foreach ($targetElements as $targetElement) {
            $targets[] = (string)$targetElement->getAttribute('name');
        }

        return $targets;
    }

    private function getTargets(DOMNode $element): array
    {
        $targets = [];
        $targetElements = $element->getElementsByTagName('target');
        foreach ($targetElements as $targetElement) {
            $targets[] = (string)$targetElement->getAttribute('name');
        }

        return $targets;
    }

    private function getValidators(DOMNode $element): array
    {
        $validators = [];
        $validatorElements = $element->getElementsByTagName('validator');
        foreach ($validatorElements as $validatorElement) {
            $disabled = (bool)$validatorElement->getAttribute('disabled');
            if ($disabled) {
                continue;
            }

            $validators[] = (string)$validatorElement->getAttribute('name');
        }

        return $validators;
    }

    private function getFilters(DOMNode $element): array
    {
        $filters = [];
        $filterElements = $element->getElementsByTagName('filter');
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
