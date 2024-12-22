<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

class ComponentHydrator
{
    public function hydrateBlock(AbstractBlock $block, Component $component): void
    {
        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof ViewModelInterface) {
            return;
        }

        $block->setViewModel($viewModel);
        $block->assign('viewModel', $viewModel);

        if (method_exists($viewModel, 'setBlock')) {
            $viewModel->setBlock($block);
        }
    }

    public function hydrateMutator(Component $component): void
    {
    }
}
