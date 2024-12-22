<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;
use Yireo\LokiComponents\Config\XmlConfig;
use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Exception\NoComponentFoundException;
use Yireo\LokiComponents\ViewModel\ComponentUtil;

class ComponentRegistry
{
    private array $components = [];

    public function __construct(
        private XmlConfig        $xmlConfig,
        private ComponentFactory $componentFactory,
        private ComponentUtil $componentUtil
    ) {
    }

    public function getComponentByName(string $componentName): Component
    {
        if (false === isset($this->components[$componentName])) {
            $this->components[$componentName] = $this->createComponentByName($componentName);
        }

        return $this->components[$componentName];
    }

    /**
     * @param string $blockName
     *
     * @return Component
     */
    public function getComponentFromBlockName(string $blockName): Component
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getSourceBlock() === $blockName) {
                return $this->getComponentByName($componentDefinition->getName());
            }
        }

        throw new NoComponentFoundException((string)__('Unknown component "%1"', $blockName));
    }

    /**
     * @param AbstractBlock $block
     *
     * @return Component
     * @throws NoComponentFoundException
     */
    public function getComponentFromBlock(AbstractBlock $block): Component
    {
        $blockName = $block->getNameInLayout();
        if (empty($blockName)) {
            throw new NoComponentFoundException((string)__('Block has no name', $block->getJsId()));
        }

        return $this->getComponentFromBlockName($blockName);
    }

    /**
     * @param AbstractBlock $block
     *
     * @return ViewModelInterface
     */
    public function getViewModelFromBlock(AbstractBlock $block): ViewModelInterface
    {
        return $this->getComponentFromBlock($block)->getViewModel();
    }

    /**
     * @param AbstractBlock $block
     *
     * @return MutatorInterface
     */
    public function getMutatorFromBlock(AbstractBlock $block): MutatorInterface
    {
        return $this->getComponentFromBlock($block)->getMutator();
    }


    /**
     * @param string $elementId
     *
     * @return string
     */
    public function getBlockNameFromElementId(string $elementId): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($this->componentUtil->getElementIdByBlockName($componentDefinition->getSourceBlock()) === $elementId) {
                return $componentDefinition->getSourceBlock();
            }
        }

        throw new NoComponentFoundException((string)__('Unknown element ID "%1"', $elementId));
    }

    /**
     * @param string $componentName
     *
     * @return Component
     */
    private function createComponentByName(string $componentName): Component
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getName() === $componentName) {
                return $this->componentFactory->createFromDefinition($componentDefinition);
            }
        }

        throw new \RuntimeException('Could not create component "' . $componentName . '"');
    }
}
