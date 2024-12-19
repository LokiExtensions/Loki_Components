<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use RuntimeException;
use Yireo\LokiComponents\Config\XmlConfig;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

// @todo: Split this up in "ComponentFinders"
class ComponentRegistry
{
    public function __construct(
        private LayoutInterface $layout,
        private XmlConfig $xmlConfig,
    ) {
    }

    /**
     * @param string $blockName
     * @return ComponentInterface
     */
    public function getComponentFromBlockName(string $blockName): ComponentInterface
    {
        try {
            return $this->getComponentFromDefinition($blockName);
        } catch (RuntimeException $e) {
        }

        try {
            return $this->getComponentFromLayout($blockName);
        } catch (RuntimeException $e) {
        }

        throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
    }

    private function getComponentFromDefinition(string $blockName): ComponentInterface
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

    private function getComponentFromLayout(string $blockName): ComponentInterface
    {
        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('Not such block "%1"', $blockName));
        }

        $component = $block->getData('component');
        if (false === $component instanceof ComponentInterface) {
            throw new RuntimeException((string)__('No component inside block "%1"', $blockName));
        }

        return $component;
    }

    public function getDomIdFromComponentName(string $componentName): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getName() === $componentName) {
                return $componentDefinition->getDomId();
            }
        }

        throw new RuntimeException((string)__('Unknown component "%1"', $componentName));
    }

    public function getComponentNameFromDomId(string $domId): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getDomId() === $domId) {
                return $componentDefinition->getName();
            }
        }

        foreach ($this->layout->getAllBlocks() as $block) {
            $possibleMatch = preg_replace('/([^a-z\-]+)/', '-', $block->getNameInLayout());
            if ($possibleMatch === $domId) {
                return $block->getNameInLayout();
            }
        }

        throw new RuntimeException((string)__('Unknown DOM ID "%1"', $domId));
    }

    public function getComponentDefinitionByComponent(ComponentInterface $component): ComponentDefinition
    {
        $componentDefinitions = $this->xmlConfig->getComponentDefinitions();
        foreach ($componentDefinitions as $componentDefinition) {
            if ($componentDefinition->getComponent() instanceof $component) {
                return $componentDefinition;
            }
        }

        throw new RuntimeException((string)__('Unknown component "%1"', get_class($component)));
    }
}
