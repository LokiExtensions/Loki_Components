<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Config;

use Magento\Framework\Config\Data as DataConfig;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

class XmlConfig extends DataConfig
{
    /**
     * @return ComponentDefinition[]
     */
    public function getComponentDefinitions(): array
    {
        static $componentDefinitions = null;
        if (!empty($componentDefinitions)) {
            return $componentDefinitions;
        }

        $componentDefinitions = [];
        $componentsData = (array)$this->get('components');
        foreach ($componentsData as $componentData) {
            if (empty($componentData)) {
                continue;
            }

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
        return new ComponentDefinition(
            $componentData['name'],
            $componentData['class'],
            $componentData['context'],
            $componentData['viewModel'],
            $componentData['repository'],
            $componentData['targets'],
            $componentData['validators'],
            $componentData['filters']
        );
    }
}
