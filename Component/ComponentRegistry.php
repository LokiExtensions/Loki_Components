<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use RuntimeException;
use Yireo\LokiComponents\Config\XmlConfig;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;
use Yireo\LokiComponents\Mutator\MutatorInterface;

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
    public function getViewModelFromBlock(AbstractBlock $block): ComponentInterface
    {
        try {
            return $this->getViewModelFromDefinition($block);
        } catch (RuntimeException $e) {
        }

        try {
            return $this->getViewModelFromLayout($block);
        } catch (RuntimeException $e) {
        }

        $blockName = $block->getNameInLayout();
        throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
    }

    private function getViewModelFromDefinition(AbstractBlock $block): ComponentInterface
    {
        $blockName = $block->getNameInLayout();
        $componentDefinitions = $this->xmlConfig->getComponentDefinitions();
        if (!isset($componentDefinitions[$blockName])) {
            throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
        }

        /** @var ComponentDefinition $componentDefinition */
        $componentDefinition = $componentDefinitions[$blockName];
        $viewModel = $componentDefinition->getViewModel();
        if (false === $viewModel instanceof ComponentInterface) {
            throw new RuntimeException((string)__('ViewModel "%1" is not a component', $blockName));
        }

        return $viewModel;
    }

    private function getViewModelFromLayout(AbstractBlock $block): ComponentInterface
    {
        $blockName = $block->getNameInLayout();

        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('Not such block "%1"', $blockName));
        }

        $viewModel = $block->getData('component');
        if (false === $viewModel instanceof ComponentInterface) {
            throw new RuntimeException((string)__('ViewModel inside block "%1" is not a component', $blockName));
        }

        return $viewModel;
    }

    /**
     * @param string $blockName
     * @return MutatorInterface
     */
    public function getMutatorFromBlock(AbstractBlock $block): MutatorInterface
    {
        $blockName = $block->getNameInLayout();

        try {
            return $this->getMutatorFromDefinition($block);
        } catch (RuntimeException $e) {
        }

        try {
            return $this->getMutatorFromLayout($block);
        } catch (RuntimeException $e) {
        }

        throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
    }

    private function getMutatorFromDefinition(AbstractBlock $block): MutatorInterface
    {
        $blockName = $block->getNameInLayout();

        $componentDefinitions = $this->xmlConfig->getComponentDefinitions();
        if (!isset($componentDefinitions[$blockName])) {
            throw new RuntimeException((string)__('Unknown component "%1"', $blockName));
        }

        /** @var ComponentDefinition $componentDefinition */
        $componentDefinition = $componentDefinitions[$blockName];
        $mutator = $componentDefinition->getMutator();
        if (false === $mutator instanceof MutatorInterface) {
            throw new RuntimeException((string)__('No mutator found for "%1"', $blockName));
        }

        return $mutator;
    }

    private function getMutatorFromLayout(AbstractBlock $block): MutatorInterface
    {
        $blockName = $block->getNameInLayout();

        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException((string)__('Not such block "%1"', $blockName));
        }

        $mutator = $block->getData('mutator');
        if (false === $mutator instanceof MutatorInterface) {
            throw new RuntimeException((string)__('Mutator inside block "%1" is not a mutator', $blockName));
        }

        return $mutator;
    }

    public function getElementIdByBlockName(string $blockName): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            foreach ($componentDefinition->getBlockDefinitions() as $blockDefinition) {
                if ($blockDefinition->getName() === $blockName) {
                    return $blockDefinition->getElementId();
                }
            }
        }

        throw new RuntimeException((string)__('Block name "%1" could not be resolved', $blockName));
    }

    public function getComponentDefinitionFromBlock(AbstractBlock $block): ComponentDefinition
    {
        return $this->getComponentDefinitionFromBlockName((string)$block->getNameInLayout());
    }

    /**
     * @param string $blockName
     * @return ComponentDefinition
     */
    public function getComponentDefinitionFromBlockName(string $blockName): ComponentDefinition
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            foreach ($componentDefinition->getBlockDefinitions() as $blockDefinition) {
                if ($blockDefinition->getName() === $blockName) {
                    return $componentDefinition;
                }
            }
        }

        throw new RuntimeException((string)__('Block name does not resolve to component "%1"', $blockName));
    }

    public function getBlockNameFromElementId(string $elementId): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            foreach ($componentDefinition->getBlockDefinitions() as $blockDefinition) {
                if ($blockDefinition->getElementId() === $elementId) {
                    return $blockDefinition->getName();
                }
            }
        }

        throw new RuntimeException((string)__('Unknown element ID "%1"', $elementId));
    }

    public function getComponentDefinitionByViewModel(ComponentInterface $viewModel): ComponentDefinition
    {
        $componentDefinitions = $this->xmlConfig->getComponentDefinitions();
        foreach ($componentDefinitions as $componentDefinition) {
            if ($componentDefinition->getViewModel() instanceof $viewModel) {
                return $componentDefinition;
            }
        }

        throw new RuntimeException((string)__('Unknown component "%1"', get_class($viewModel)));
    }
}
