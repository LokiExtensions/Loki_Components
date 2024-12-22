<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

// @todo: Lazyload mutator and ViewModel
class Component
{
    public function __construct(
        private string $name,
        private string $sourceBlock,
        private ?ViewModelInterface $viewModel = null,
        private ?MutatorInterface $mutator = null,
        private array $targetBlocks = []
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSourceBlock(): string
    {
        return $this->sourceBlock;
    }

    public function getViewModel(): ?ViewModelInterface
    {
        return $this->viewModel;
    }

    public function getMutator(): ?MutatorInterface
    {
        return $this->mutator;
    }

    public function getTargetBlocks(): array
    {
        return $this->targetBlocks;
    }
}
