<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Controller;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class LayoutLoader
{
    public function __construct(
        private readonly ResultPageFactory $resultPageFactory,
    ) {
    }

    public function load(array $handles = []): LayoutInterface
    {
        $resultPage = $this->resultPageFactory->create();

        if (!empty($handles)) {
            foreach ($handles as $handle) {
                $handle = preg_replace('/([^a-z0-9\-\_]+)/', '', $handle);
                $resultPage->addHandle($handle);
            }
        }

        return $resultPage->getLayout();
    }
}
