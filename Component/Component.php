<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

// @todo: Lazyload mutator and ViewModel
class Component implements ComponentInterface
{
    public function __construct(
        protected string              $name,
        protected string              $sourceBlock,
        protected array               $targetBlocks,
        protected ?ViewModelInterface $viewModel = null,
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

    public function getTargetBlocks(): array
    {
        return $this->targetBlocks;
    }
}
