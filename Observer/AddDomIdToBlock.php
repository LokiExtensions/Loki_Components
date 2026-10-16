<?php
declare(strict_types=1);

namespace Loki\Components\Observer;

use Loki\Components\Util\Block\GetElementId;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;

class AddDomIdToBlock implements ObserverInterface
{
    public function __construct(
        private readonly GetElementId $getElementId,
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
        $html = trim((string)$transport->getHtml(), " \f\n\r\t\v\x00");
        if (empty($html)) {
            return;
        }

        if (!preg_match('#^<([a-z][a-z0-9-]*)(?=[\s/>])((?:"[^"]*"|\'[^\']*\'|[^\'">])*)>#i', $html, $match)) {
            return;
        }

        if (preg_match('#(^|\s)id\s*=#i', $match[2])) {
            return;
        }

        $elementId = $this->getElementId->execute($block);
        $newTag = '<'.$match[1].' id="'.$elementId.'"'.$match[2].'>';
        $html = $newTag.substr($html, strlen($match[0]));

        $transport->setHtml($html);
    }
}
