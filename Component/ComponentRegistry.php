<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Config\XmlConfig;
use Yireo\LokiComponents\Exception\NoComponentFoundException;
use Yireo\LokiComponents\Util\ComponentUtil;

class ComponentRegistry
{
    private array $components = [];

    public function __construct(
        private XmlConfig $xmlConfig,
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
        // @todo: Rewrite this to array_filter function
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getName() === $blockName) {
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
     * @param string $elementId
     *
     * @return string
     */
    public function getBlockNameFromElementId(string $elementId): string
    {
        // @todo: Rewrite this to array_filter function
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            foreach ($componentDefinition->getTargets() as $target) {
                if ($target === $elementId) {
                    return $target;
                }

                if ($this->componentUtil->getElementIdByBlockName($target) === $elementId) {
                    return $target;
                }
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
        // @todo: Rewrite this to array_filter function
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getName() === $componentName) {
                return $this->componentFactory->createFromDefinition($componentDefinition);
            }
        }

        throw new \RuntimeException('Could not create component "'.$componentName.'"');
    }
}
