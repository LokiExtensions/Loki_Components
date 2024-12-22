<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\ObjectManagerInterface;
use UnexpectedValueException;
use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;
use Yireo\LokiComponents\Config\XmlConfig\Definition\ComponentDefinition;

class ComponentFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function createFromDefinition(ComponentDefinition $componentDefinition): Component
    {
        $arguments = [
            'name' => $componentDefinition->getName(),
            'sourceBlock' => $componentDefinition->getSourceBlock(),
            'targetBlocks' => $componentDefinition->getTargetBlocks(),
        ];

        $viewModel = $componentDefinition->getViewModel();
        if (!empty($viewModel)) {
            $arguments['viewModel'] = $this->getViewModel($viewModel);
        }


        $mutator = $componentDefinition->getMutator();
        if (!empty($mutator)) {
            $arguments['mutator'] = $this->getMutator($mutator);
        }

        try {
            return $this->objectManager->create(Component::class, $arguments);
        } catch(UnexpectedValueException $exception) {
            throw new UnexpectedValueException('Failed to create component: '.var_export($arguments, true));
        }
    }

    private function getViewModel(string $viewModelClass): ViewModelInterface
    {
        return $this->objectManager->get($viewModelClass);
    }

    private function getMutator(string $mutator): MutatorInterface
    {
        return $this->objectManager->get($mutator);
    }
}
