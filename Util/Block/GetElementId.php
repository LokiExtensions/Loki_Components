<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use Loki\Components\Util\IdConvertor;
use Magento\Framework\View\Element\AbstractBlock;

class GetElementId
{
    public function __construct(
        private readonly IdConvertor $idConvertor,
    ) {
    }

    public function execute(AbstractBlock $block): string
    {
        $uniqId = (string)$block->getUniqId();
        if (strlen($uniqId) > 0) {
            return $this->idConvertor->toElementId($uniqId);
        }

        $nameInLayout = strtolower((string)$block->getNameInLayout());
        return $this->idConvertor->toElementId($nameInLayout);
    }
}
