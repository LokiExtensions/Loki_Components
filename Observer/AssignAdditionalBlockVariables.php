<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Factory\ViewModelFactory;
use Loki\Components\Util\Block\BlockRenderer;
use Loki\Components\Util\Block\ChildRenderer;
use Loki\Components\Util\Block\CssClassFactory;
use Loki\Components\Util\Block\TemplateRenderer;

class AssignAdditionalBlockVariables implements ObserverInterface
{
    public function __construct(
        private readonly ViewModelFactory $viewModelFactory,
        private readonly CssClassFactory $cssClassFactory,
        private readonly BlockRenderer $blockRenderer,
        private readonly ChildRenderer $childRenderer,
        private readonly TemplateRenderer $templateRenderer,
        private readonly ComponentRegistry $componentRegistry
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        $cssClass = $this->cssClassFactory->create();
        $block->assign('css', $cssClass->setBlock($block));
        $block->assign('viewModelFactory', $this->viewModelFactory);
        $block->assign('blockRenderer', $this->blockRenderer);
        $block->assign('childRenderer', $this->childRenderer);
        $block->assign('templateRenderer', $this->templateRenderer);

        $this->disallowRendering($block);
    }

    private function disallowRendering(AbstractBlock $block): void
    {
        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return;
        }

        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof ComponentViewModelInterface) {
            return;
        }

        if ($viewModel->isAllowRendering()) {
            return;
        }

        $block->setTemplate('Loki_Components::utils/not-rendered.phtml');
        $block->setNotRendered(true);
    }
}
