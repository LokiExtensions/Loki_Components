<?php declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Loki\Components\Config\XmlConfig;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Util\IdConvertor;

class ComponentRegistry
{
    private array $components = [];

    public function __construct(
        private readonly XmlConfig $xmlConfig,
        private readonly ComponentFactory $componentFactory,
        private readonly IdConvertor $idConvertor
    ) {
    }

    /**
     * @param string $componentName
     * @return Component
     * @throws NoComponentFoundException
     */
    public function getComponentByName(string $componentName): Component
    {
        if (false === isset($this->components[$componentName])) {
            $this->components[$componentName] = $this->createComponentByName($componentName);
        }

        return $this->components[$componentName];
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

        return $this->getComponentByName($blockName);
    }

    /**
     * @param string $elementId
     *
     * @return string
     */
    public function getBlockNameFromElementId(string $elementId): string
    {
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($this->idConvertor->toElementId($componentDefinition->getName()) === $elementId) {
                return $componentDefinition->getName();
            }

            foreach ($componentDefinition->getTargets() as $target) {
                if ($target === $elementId) {
                    return $target;
                }

                if ($this->idConvertor->toElementId($target) === $elementId) {
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
        foreach ($this->xmlConfig->getComponentDefinitions() as $componentDefinition) {
            if ($componentDefinition->getName() === $componentName) {
                return $this->componentFactory->createFromDefinition($componentDefinition);
            }
        }

        throw new NoComponentFoundException('Could not create component "' . $componentName . '"');
    }
}
