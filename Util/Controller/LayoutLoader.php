<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class LayoutLoader
{
    public function __construct(
        private readonly ResultPageFactory $resultPageFactory,
        private readonly LayoutHandlerComposite $layoutHandlerComposite
    ) {
    }

    public function load(array $handles = []): LayoutInterface
    {
        $handles = $this->layoutHandlerComposite->getHandles($handles);
        $resultPage = $this->resultPageFactory->create();

        if ($handles !== []) {
            foreach ($handles as $handle) {
                $handle = preg_replace('/([^a-z0-9\-\_]+)/', '', $handle);
                $resultPage->addHandle($handle);
            }
        }

        return $resultPage->getLayout();
    }
}
