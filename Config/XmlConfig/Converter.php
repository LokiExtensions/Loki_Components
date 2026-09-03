<?php declare(strict_types=1);

namespace Loki\Components\Config\XmlConfig;

use DOMDocument;
use DOMElement;
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
            'groups' => $this->getGroupDefinitions($source),
            'components' => $this->getComponentDefinitions($source),
        ];
    }

    /**
     * @param DOMDocument $source
     * @return array[]
     */
    private function getComponentDefinitions(DOMDocument $source): array
    {
        $componentDefinitions = [];
        $componentElements = $source->getElementsByTagName('component');

        foreach ($componentElements as $componentElement) {
            $name = (string)$componentElement->getAttribute('name');
            $groupName = (string)$componentElement->getAttribute('group');
            if ($groupName === '' || $groupName === '0') {
                $groupName = 'default';
            }

            $componentDefinitions[$name] = [
                'name' => $name,
                'group' => $groupName,
                'class' => (string)$componentElement->getAttribute('class'),
                'context' => (string)$componentElement->getAttribute('context'),
                'viewModel' => (string)$componentElement->getAttribute('viewModel'),
                'repository' => (string)$componentElement->getAttribute('repository'),
                'targets' => $this->getTargets($componentElement),
                'validators' => $this->getValidators($componentElement),
                'filters' => $this->getFilters($componentElement),
            ];
        }

        return $componentDefinitions;
    }

    /**
     * @param DOMDocument $source
     * @return array[]
     */
    private function getGroupDefinitions(DOMDocument $source): array
    {
        $groupDefinitions = [];
        $groupElements = $source->getElementsByTagName('group');
        foreach ($groupElements as $groupElement) {
            $groupName = (string)$groupElement->getAttribute('name');

            $groupDefinitions[$groupName] = [
                'name' => $groupName,
                'class' => (string)$groupElement->getAttribute('class'),
                'context' => (string)$groupElement->getAttribute('context'),
                'viewModel' => (string)$groupElement->getAttribute('viewModel'),
                'repository' => (string)$groupElement->getAttribute('repository'),
                'targets' => $this->getTargets($groupElement),
            ];
        }

        return $groupDefinitions;
    }

    private function getTargets(DOMElement $element): array
    {
        $targets = [];
        $targetElements = $element->getElementsByTagName('target');
        foreach ($targetElements as $targetElement) {
            $targetName = (string)$targetElement->getAttribute('name');
            $targets[$targetName] = [
                'name' => $targetName,
                'disabled' => (bool)$targetElement->getAttribute('disabled'),
            ];
        }

        return $targets;
    }

    private function getValidators(DOMElement $element): array
    {
        $validators = [];
        $validatorElements = $element->getElementsByTagName('validator');
        foreach ($validatorElements as $validatorElement) {
            $validatorName = (string)$validatorElement->getAttribute('name');
            $validators[$validatorName] = [
                'name' => $validatorName,
                'disabled' => (bool)$validatorElement->getAttribute('disabled'),
            ];
        }

        return $validators;
    }

    private function getFilters(DOMElement $element): array
    {
        $filters = [];
        $filterElements = $element->getElementsByTagName('filter');
        foreach ($filterElements as $filterElement) {
            $filterName = (string)$filterElement->getAttribute('name');
            $filters[$filterName] = [
                'name' => $filterName,
                'disabled' => (bool)$filterElement->getAttribute('disabled'),
            ];
        }

        return $filters;
    }
}
