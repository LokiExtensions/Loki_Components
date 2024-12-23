<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;
use Yireo\LokiComponents\Component\Component;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;
use Yireo\LokiComponents\Component\ViewModelInterface;
use Yireo\LokiComponents\Exception\NoComponentFoundException;
use Yireo\LokiComponents\Util\ComponentUtil;

class AddComponent implements ObserverInterface
{
    public function __construct(
        private ComponentRegistry $componentRegistry,
        private ComponentUtil $componentUtil,
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return;
        }

        $component->setCurrentSource($block);
        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof ComponentViewModelInterface) {
            return;
        }

        $block->setViewModel($viewModel);
        $block->assign('viewModel', $viewModel);
        $block->assign('componentUtil', $this->componentUtil);
    }
}
