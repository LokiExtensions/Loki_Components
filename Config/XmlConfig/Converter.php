<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use DOMDocument;
use DOMNode;
use Magento\Framework\Config\ConverterInterface;
use RuntimeException;
use Yireo\LokiComponents\Component\Component;
use Yireo\LokiComponents\Component\ComponentViewModel;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;
use Yireo\LokiComponents\Util\DefaultTargets;

class Converter implements ConverterInterface
{
    public function __construct(
        private readonly DefaultTargets $defaultTargets,
    ) {
    }

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
            if ($groupName === '' || $groupName === '0') {
                $groupName = 'default';
            }

            if (false === array_key_exists($groupName, $groupDefinitions)) {
                throw new RuntimeException('Component "' . $name . '" refers to unknown group "' . $groupName . '"');
            }

            $group = $groupDefinitions[$groupName];
            $componentClass = (string)$componentElement->getAttribute('class');
            $context = (string)$componentElement->getAttribute('context');
            $viewModel = (string)$componentElement->getAttribute('viewModel');
            $repository = (string)$componentElement->getAttribute('repository');

            $componentDefinitions[$name] = [
                'name' => $name,
                'class' => $componentClass === '' || $componentClass === '0' ? $group['class'] : $componentClass,
                'context' => $context === '' || $context === '0' ? $group['context'] : $context,
                'viewModel' => $viewModel === '' || $viewModel === '0' ? $group['viewModel'] : $viewModel,
                'repository' => $repository === '' || $repository === '0' ? $group['repository'] : $repository,
                'targets' => $this->getTargets($componentElement, $name, $group['targets']),
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
            if ($groupClass === '' || $groupClass === '0') {
                $groupClass = Component::class;
            }

            $viewModelClass = (string)$groupElement->getAttribute('viewModel');
            if ($viewModelClass === '' || $viewModelClass === '0') {
                $viewModelClass = ComponentViewModel::class;
            }

            $groupDefinitions[$groupName] = [
                'class' => $groupClass,
                'context' => (string)$groupElement->getAttribute('context'),
                'viewModel' => $viewModelClass,
                'repository' => (string)$groupElement->getAttribute('repository'),
                'targets' => $this->getTargets($groupElement),
            ];
        }

        return $groupDefinitions;
    }

    private function getTargets(DOMNode $element, string $blockName = '', array $targets = []): array
    {
        $disabledTargets = [];
        if ($blockName !== '' && $blockName !== '0') {
            $targets[] = $blockName;
        }

        $targetElements = $element->getElementsByTagName('target');
        foreach ($targetElements as $targetElement) {
            $targetName = (string)$targetElement->getAttribute('name');
            if ($targetName === 'self') {
                $targetName = $blockName;
            }

            if ((bool)$targetElement->getAttribute('disabled')) {
                $disabledTargets[] = $targetName;
            } else {
                $targets[] = $targetName;
            }
        }

        $targets = array_merge($targets, $this->defaultTargets->getTargets());
        $targets = array_diff($targets, $disabledTargets);
        return array_values(array_unique($targets));
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
