<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use DOMDocument;
use DOMNode;
use Magento\Framework\Config\ConverterInterface;
use RuntimeException;
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
        $groupDefinitions = $this->getGroupDefinitions($source);
        $componentDefinitions = [];
        $componentElements = $source->getElementsByTagName('component');

        foreach ($componentElements as $componentElement) {
            $name = (string)$componentElement->getAttribute('name');
            $groupName = (string)$componentElement->getAttribute('group');
            if (empty($groupName)) {
                $groupName = 'default';
            }

            if (false === array_key_exists($groupName, $groupDefinitions)) {
                throw new RuntimeException('Component "'.$name.'" refers to unknown group "'.$groupName.'"');
            }

            $group = $groupDefinitions[$groupName];
            $componentClass = (string)$componentElement->getAttribute('class');
            $context = (string)$componentElement->getAttribute('context');
            $viewModel = (string)$componentElement->getAttribute('viewModel');
            $repository = (string)$componentElement->getAttribute('repository');

            $componentDefinitions[$name] = [
                'name' => $name,
                'class' => !empty($componentClass) ? $componentClass : $group['class'],
                'context' => !empty($context) ? $context : $group['context'],
                'viewModel' => !empty($viewModel) ? $viewModel : $group['viewModel'],
                'repository' => !empty($repository) ? $repository : $group['repository'],
                'targets' => array_merge($group['targets'], $this->getTargets($componentElement)),
                'validators' => $this->getValidators($componentElement),
                'filters' => $this->getFilters($componentElement),
            ];
        }

        return $componentDefinitions;
    }

    private function getGroupDefinitions(DOMNode $element): array
    {
        $groupDefinitions = [];
        $groupElements = $element->getElementsByTagName('group');
        foreach ($groupElements as $groupElement) {
            $groupName = (string)$groupElement->getAttribute('name');
            $groupClass = (string)$groupElement->getAttribute('class');
            if (empty($groupClass)) {
                $groupClass = Component::class;
            }

            $groupDefinitions[$groupName] = [
                'class' => $groupClass,
                'context' => (string)$groupElement->getAttribute('context'),
                'viewModel' => (string)$groupElement->getAttribute('viewModel'),
                'repository' => (string)$groupElement->getAttribute('repository'),
                'targets' => $this->getTargets($groupElement),
            ];
        }

        return $groupDefinitions;
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
