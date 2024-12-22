<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

// @todo: Lazyload mutator and ViewModel
class MutableComponent extends Component implements MutableComponentInterface
{
    protected mixed $value = null;

    public function __construct(
        string $name,
        string $sourceBlock,
        array $targetBlocks,
        ViewModelInterface $viewModel,
        protected MutatorInterface $mutator,
        protected Messages $messages,
        protected array $validators = [],
        protected array $filters = []
    ) {
        parent::__construct($name, $sourceBlock, $targetBlocks, $viewModel);
    }

    public function getViewModel(): ViewModelInterface
    {
        return $this->viewModel;
    }

    public function getMutator(): MutatorInterface
    {
        return $this->mutator;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getMessages(): Messages
    {
        return $this->messages;
    }
}
