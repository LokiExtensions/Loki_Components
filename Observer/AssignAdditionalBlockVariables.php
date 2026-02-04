<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\Block\ImageRenderer;
use Loki\Components\Util\ComponentUtil;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Loki\Components\Component\ComponentRegistry;
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
        private readonly ComponentUtil $componentUtil,
        private readonly array $blockPrefixes = []
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        $component = $this->getComponent($block);
        if ($component instanceof ComponentInterface) {
            $block = $this->addComponent($block, $component);
        }

        if ($component instanceof ComponentInterface || $this->allowVariables($block)) {
            $this->addVariables($block);
        }
    }

    private function getComponent(Template $block): ?ComponentInterface
    {
        try {
            return $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return null;
        }
    }

    private function addComponent(Template $block, ComponentInterface $component): Template
    {
        $viewModel = $component->getViewModel();
        $template = (string)$viewModel->getTemplate();
        if (strlen($template) > 0) {
            $block->setTemplate($template); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock
        }

        $block->setViewModel($viewModel);
        $block->setViewModelInstance($viewModel);

        if (false === $viewModel->isVisible()) {
            $block->setTemplate('Loki_Components::utils/not-visible.phtml'); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock
        }

        if (false === $viewModel->isAllowRendering()) {
            $block->setTemplate('Loki_Components::utils/not-rendered.phtml'); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock
            $block->setNotRendered(true);
            return $block;
        }

        $block->assign('viewModel', $viewModel);
        $block->assign('componentUtil', $this->componentUtil);
        return $block;
    }

    private function addVariables(Template $block): void
    {
        $block->assign('viewModelFactory', $this->viewModelFactory);
        $block->assign('blockRenderer', $this->blockRenderer);
        $block->assign('childRenderer', $this->childRenderer);
        $block->assign('templateRenderer', $this->templateRenderer);
        $block->assign('imageRenderer', $this->imageRenderer);
    }

    private function allowVariables(AbstractBlock $block): bool
    {
        if (str_starts_with((string)$block->getTemplate(), 'Loki')) {
            return true;
        }

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

