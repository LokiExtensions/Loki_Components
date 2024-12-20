<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

use Magento\Framework\App\ObjectManager;
use Yireo\LokiComponents\Component\MutatorInterface;
use Yireo\LokiComponents\Component\ComponentInterface;

class ComponentDefinition
{
    public function __construct(
        private string $name,
        private string $viewModel = '',
        private string $mutator = '',
        private array $blockDefinitions = []
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getViewModel(): ?ComponentInterface
    {
        if (empty($this->viewModel)) {
            return null;
        }

        return ObjectManager::getInstance()->get($this->viewModel);
    }

    public function getMutator(): ?MutatorInterface
    {
        if (empty($this->mutator)) {
            return null;
        }

        return ObjectManager::getInstance()->get($this->mutator);
    }

    /**
     * @return BlockDefinition[]
     */
    public function getBlockDefinitions(): array
    {
        return $this->blockDefinitions;
    }
}
