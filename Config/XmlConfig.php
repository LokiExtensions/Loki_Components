<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Config;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\Data as DataConfig;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

class XmlConfig extends DataConfig
{
    /**
     * @return ComponentDefinition[]
     */
    public function getComponentDefinitions(): array
    {
        $componentDefinitions = [];
        $componentsData = $this->get('components');
        foreach($componentsData as $componentData) {
            $name = $componentData['name'];
            $componentDefinitions[$name] = $this->createComponentDefinition($componentData);
        }

        return $componentDefinitions;
    }

    /**
     * @param array $componentData
     * @return ComponentDefinition
     */
    private function createComponentDefinition(array $componentData): ComponentDefinition
    {
        $componentInstance = null;
        if (!empty($componentData['className'])) {
            $componentInstance = ObjectManager::getInstance()->get($componentData['className']);
        }

        return new ComponentDefinition(
            $componentData['name'],
            $componentData['domId'],
            $componentInstance,
        );
    }
}
