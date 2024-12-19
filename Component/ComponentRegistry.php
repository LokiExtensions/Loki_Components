<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use RuntimeException;
use Yireo\LokiComponents\Config\XmlConfig;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

class ComponentRegistry
{
    public function __construct(
        private XmlConfig $xmlConfig,
    ) {
    }

    /**
     * @param string $blockName
     * @return ComponentInterface
     */
    public function getComponentFromBlockName(string $blockName): ComponentInterface
    {
        $componentDefinitions = $this->xmlConfig->getComponentDefinitions();
        if (!isset($componentDefinitions[$blockName])) {
            throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
        }

        /** @var ComponentDefinition $componentDefinition */
        $componentDefinition = $componentDefinitions[$blockName];
        $component = $componentDefinition->getComponent();
        if (false === $component instanceof ComponentInterface) {
            throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
        }

        return $component;
    }

    public function getDomIdFromComponentName(string $componentName): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if($componentDefinition->getName() === $componentName) {
                return $componentDefinition->getDomId();
            }
        }

        throw new RuntimeException((string)__('Unknown component "%1"', $componentName));
    }
    public function getComponentNameFromDomId(string $domId): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if($componentDefinition->getDomId() === $domId) {
                return $componentDefinition->getName();
            }
        }

        throw new RuntimeException((string)__('Unknown DOM ID "%1"', $domId));
    }
}
