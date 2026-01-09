<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Framework\ObjectManagerInterface;
use UnexpectedValueException;
use Loki\Components\Config\XmlConfig\Definition\ComponentDefinition;

class ComponentFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function createFromDefinition(ComponentDefinition $componentDefinition): Component
    {
        $contextClass = $componentDefinition->getContext();
        if ($contextClass === null || $contextClass === '' || $contextClass === '0') {
            $contextClass = ComponentContext::class;
        }

        $context = $this->objectManager->get($contextClass);

        $arguments = [
            'name' => $componentDefinition->getName(),
            'context' => $context,
            'targets' => $componentDefinition->getTargets(),
            'viewModelClass' => $componentDefinition->getViewModelClass(),
            'repositoryClass' => $componentDefinition->getRepositoryClass(),
            'validators' => $componentDefinition->getValidators(),
            'filters' => $componentDefinition->getFilters(),
        ];

        $componentClass = $componentDefinition->getClassName();

        try {
            /** @var Component $component */
            $component = $this->objectManager->create($componentClass, $arguments);
            return $component;
        } catch (UnexpectedValueException $exception) {
            throw new UnexpectedValueException("Failed to create component: \n"
            . json_encode($arguments) . "\n"
            . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
