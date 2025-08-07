<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Util\ComponentUtil;
use Magento\Framework\View\Element\Template;

class AddComponentToBlock implements ObserverInterface
{
    public function __construct(
        private readonly ComponentRegistry $componentRegistry,
        private readonly ComponentUtil $componentUtil,
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof AbstractBlock) {
            return;
        }

        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (NoComponentFoundException $exception) {
            return;
        }

        $viewModel = $component->getViewModel();
        $template = $viewModel->getTemplate();
        if ($template !== null && $template !== '' && $template !== '0') {
            $block->setTemplate($template);
        }

        $block->setViewModel($viewModel);

        if (false === $block instanceof Template) {
            return;
        }

        $block->assign('viewModel', $viewModel);
        $block->assign('componentUtil', $this->componentUtil);
    }
}
