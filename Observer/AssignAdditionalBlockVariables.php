<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Loki\Components\Util\Block\ImageRenderer;
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
use Loki\Components\Util\Block\TemplateRenderer;

class AssignAdditionalBlockVariables implements ObserverInterface
{
    public function __construct(
        private readonly ViewModelFactory $viewModelFactory,
        private readonly BlockRenderer $blockRenderer,
        private readonly ChildRenderer $childRenderer,
        private readonly TemplateRenderer $templateRenderer,
        private readonly ImageRenderer $imageRenderer,
        private readonly ComponentRegistry $componentRegistry,
        private readonly array $blockPrefixes = []
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        if (false === $this->allowVariables($block)) {
            return;
        }

        $block->assign('viewModelFactory', $this->viewModelFactory);
        $block->assign('blockRenderer', $this->blockRenderer->setAncestorBlock($block));
        $block->assign('childRenderer', $this->childRenderer->setAncestorBlock($block));
        $block->assign('templateRenderer', $this->templateRenderer->setAncestorBlock($block));
        $block->assign('imageRenderer', $this->imageRenderer->setBlock($block));

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

    private function allowVariables(AbstractBlock $block): bool
    {
        foreach ($this->blockPrefixes as $blockPrefix) {
            $blockName = (string)$block->getNameInLayout();
            if (empty($blockName)) {
                continue;
            }

            if (str_starts_with($blockName, $blockPrefix)) {
                return true;
            }

            if ($blockName === $blockPrefix) {
                return true;
            }
        }

        return false;
    }
}
