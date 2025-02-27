<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Yireo\LokiComponents\Util\Block\ChildRenderer;
use Yireo\LokiComponents\Util\Block\TemplateRenderer;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;
use Yireo\LokiComponents\Exception\NoComponentFoundException;
use Yireo\LokiComponents\Factory\ViewModelFactory;
use Yireo\LokiComponents\Util\Block\BlockRenderer;
use Yireo\LokiComponents\Util\Block\CssClassFactory;

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

        $block->setTemplate('Yireo_LokiComponents::utils/not-rendered.phtml');
        $block->setNotRendered(true);
    }
}
