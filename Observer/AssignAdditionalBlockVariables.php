<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;
use Yireo\LokiComponents\Factory\ViewModelFactory;
use Yireo\LokiComponents\Util\Block\CssClassFactory;

class AssignAdditionalBlockVariables implements ObserverInterface
{
    public function __construct(
        private readonly ViewModelFactory $viewModelFactory,
        private readonly CssClassFactory $cssClassFactory,
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
    }
}
