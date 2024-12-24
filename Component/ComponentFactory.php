<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\ObjectManagerInterface;
use UnexpectedValueException;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

class ComponentFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function createFromDefinition(ComponentDefinition $componentDefinition): Component
    {
        $contextClass = $componentDefinition->getContext();
        if (empty($contextClass)) {
            $contextClass = ComponentContext::class;
        }

        $arguments = [
            'name' => $componentDefinition->getName(),
            'context' => $this->objectManager->get($contextClass),
            'targets' => $componentDefinition->getTargets(),
            'viewModelClass' => $componentDefinition->getViewModel(),
            'repositoryClass' => $componentDefinition->getRepository(),
            'validators' => $componentDefinition->getValidators(),
            'filters' => $componentDefinition->getFilters(),
        ];

        $componentClass = $componentDefinition->getClassName();
        try {
            return $this->objectManager->create($componentClass, $arguments);
        } catch(UnexpectedValueException $exception) {
            throw new UnexpectedValueException(
                "Failed to create component: \n"
                .json_encode($arguments)."\n"
                .$exception->getMessage());
        }
    }
}
