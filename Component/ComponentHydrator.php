<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

class ComponentHydrator
{
    public function __construct(
        private LayoutInterface $layout
    ) {
    }

    public function hydrate(Component $component): void
    {
        $this->hydrateViewModel($component);
        $this->hydrateMutator($component);
    }

    private function hydrateViewModel(Component $component): void
    {
        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof ViewModelInterface) {
            return;
        }

        $block = $this->layout->getBlock($component->getSourceBlock());
        if (false === $block instanceof AbstractBlock) {
            return;
        }

        $block->setViewModel($viewModel);
        $block->assign('viewModel', $viewModel);

        if (method_exists($viewModel, 'setBlock')) {
            $viewModel->setBlock($block);
        }
    }

    private function hydrateMutator(MutableComponentInterface $component): void
    {
        $mutator = $component->getMutator();
        if (false === $mutator instanceof MutatorInterface) {
            return;
        }

        $mutator->setComponent($component);
    }

}
