<?php
declare(strict_types=1);

namespace Loki\Components\Config;

use Magento\Framework\Config\Data as DataConfig;
use Loki\Components\Config\XmlConfig\Definition\ComponentDefinition;

class XmlConfig extends DataConfig
{
    private array $componentDefinitions = [];

    /**
     * @return ComponentDefinition[]
     */
    public function getComponentDefinitions(): array
    {
        if (!empty($this->componentDefinitions)) {
            return $this->componentDefinitions;
        }

        $componentsData = (array)$this->get('components');
        foreach ($componentsData as $componentData) {
            if (empty($componentData)) {
                continue;
            }

            $name = $componentData['name'];
            $this->componentDefinitions[$name] = $this->createComponentDefinition($componentData);
        }

        return $this->componentDefinitions;
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
