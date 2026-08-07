<?php

declare(strict_types=1);

namespace Loki\Components\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Test implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly AppState $appState,
    ) {
    }

    public function execute(): Page
    {
        if ($this->appState->getMode() !== AppState::MODE_DEVELOPER) {
            throw new NotFoundException(__('Not found'));
        }

        return $this->pageFactory->create();
    }
}
