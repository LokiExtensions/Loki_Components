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

        if (!empty($componentData['blocks'])) {
            foreach ($componentData['blocks'] as $blockData) {
                if (isset($blockData['role']) && $blockData['role'] === 'source') {
                    $sourceBlock = $blockData['name'];
                    continue;
                }

                $targetBlocks[] = $blockData['name'];
            }
        }

        if (empty($sourceBlock)) {
            throw new XmlConfigException('Component "' . $componentName . '" does not have block with role "source" defined');
        }

        return new ComponentDefinition(
            $componentName,
            $viewModel,
            $mutator,
            $sourceBlock,
            $targetBlocks
        );
    }
}
