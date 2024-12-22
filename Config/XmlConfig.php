<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Config;

use Magento\Framework\Config\Data as DataConfig;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;
use Yireo\LokiComponents\Exception\XmlConfigException;

class XmlConfig extends DataConfig
{
    /**
     * @return ComponentDefinition[]
     */
    public function getComponentDefinitions(): array
    {
        $componentDefinitions = [];
        $componentsData = $this->get('components');
        foreach ($componentsData as $componentData) {
            $name = $componentData['name'];
            $componentDefinitions[$name] = $this->createComponentDefinition($componentData);
        }

        return $componentDefinitions;
    }

    /**
     * @param array $componentData
     *
     * @return ComponentDefinition
     */
    private function createComponentDefinition(array $componentData): ComponentDefinition
    {
        $componentName = $componentData['name'];
        $viewModel = $componentData['viewModel'];
        $mutator = $componentData['mutator'];
        $sourceBlock = null;
        $targetBlocks = [];
        $validators = [];
        $filters = [];

        if (!empty($componentData['blocks'])) {
            foreach ($componentData['blocks'] as $blockData) {
                if (isset($blockData['role']) && $blockData['role'] === 'source') {
                    $sourceBlock = $blockData['name'];
                }

                $targetBlocks[] = $blockData['name'];
            }
        }

        if (empty($sourceBlock)) {
            throw new XmlConfigException('Component "' . $componentName . '" does not have block with role "source" defined');
        }


        if (!empty($componentData['validators'])) {
            foreach ($componentData['validators'] as $validatorData) {
                if (!isset($validatorData['disabled']) || $validatorData['disabled'] === 'false') {
                    $validators[] = $validatorData['name'];
                }
            }
        }

        if (!empty($componentData['filters'])) {
            foreach ($componentData['filters'] as $filterData) {
                if (!isset($filterData['disabled']) || $filterData['disabled'] === 'false') {
                    $filters[] = $filterData['name'];
                }
            }
        }

        return new ComponentDefinition(
            $componentName,
            $viewModel,
            $mutator,
            $sourceBlock,
            $targetBlocks,
            $validators,
            $filters
        );
    }
}
