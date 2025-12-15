<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;

class AddDomIdToBlock implements ObserverInterface
{
    public function __construct(
        private readonly array $blockNames = [],
    ) {
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (false === $block instanceof Template) {
            return;
        }

        $blockName = (string)$block->getNameInLayout();
        if (false === in_array($blockName, $this->blockNames)) {
            return;
        }

        $transport = $observer->getEvent()->getTransport();
        $html = trim((string)$transport->getHtml());
        if (empty($html)) {
            return;
        }

        if (!preg_match('#^<([a-z]{2,})([^>]{0,})$#mi', $html, $match)) {
            return;
        }

        if (empty($match[1])) {
            return;
        }

        if (str_contains($match[2], 'id="')) {
            return;
        }

        $elementId = $this->getElementId($block);
        $originalString = '<'.$match[1].$match[2];
        $newString = '<'.$match[1].' id="'.$elementId.'"'.$match[2];
        $html = preg_replace('#^'.$originalString.'#', $newString, $html);

        $transport->setHtml($html);
    }

    private function getElementId(AbstractBlock $block): string
    {
        $uniqId = (string)$block->getUniqId();
        if (strlen($uniqId) > 0) {
            return $uniqId;
        }

        $nameInLayout = strtolower((string)$block->getNameInLayout());
        return preg_replace('#([^a-zA-Z0-9]+)#', '-', $nameInLayout);
    }
}
