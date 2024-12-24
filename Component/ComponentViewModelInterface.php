<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\LokiComponents\Messages\MessageManager;

interface ComponentViewModelInterface extends ArgumentInterface
{
    public function getData(): mixed;

    public function getBlock(): AbstractBlock;

    public function getMessageManager(): MessageManager;
}
