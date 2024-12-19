<?php

declare(strict_types=1);

namespace Yireo\LokiComponents\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use RuntimeException;
use Yireo\LokiComponents\Component\MutableComponentInterface;
use Yireo\LokiComponents\Controller\HtmlResultFactory;
use Yireo\LokiComponents\Controller\HtmlResult;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Yireo\LokiComponents\ViewModel\Debugger;

class Html implements HttpPostActionInterface, HttpGetActionInterface
{
    private array $htmlParts = [];

    public function __construct(
        private readonly LayoutFactory $layoutFactory,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly RequestInterface $request,
        private readonly ComponentRegistry $componentRegistry,
        private readonly MessageManager $messageManager,
        private readonly Debugger $debugger
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $this->securityCheck();

        $blockName = $this->request->getParam('block');
        if (empty($blockName)) {
            throw new RuntimeException('Block name not specified');
        }

        $this->debugger->add('block', $blockName);
        $this->modifyData($blockName);

        $this->renderBlocks($this->getTargetBlockNames($blockName));
        return $this->getHtmlResult();
    }

    private function securityCheck(): void
    {
        /** @var Http $request */
        $request = $this->request;
        if ($request->getHeader('X-Alpine-Request') !== 'true') {
            throw new RuntimeException('Not an Alpine request');
        }
    }

    private function modifyData(string $blockName): void
    {
        try {
            $component = $this->componentRegistry->getComponentFromBlockName($blockName);
        } catch (\RuntimeException $e) {
            //$this->messageManager->addErrorMessage($e->getMessage());
        }

        if (empty($component)) {
            return;
        }

        if (false === $component instanceof MutableComponentInterface) {
            return;
        }

        $this->debugger->add('componentClass', get_class($component));

        try {
            $component->mutate($this->request->getParams());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    private function renderBlocks(array $blockNames): void
    {
        $layout = $this->getLayout();
        foreach ($blockNames as $blockName) {
            $block = $layout->getBlock($blockName);
            if ($block instanceof BlockInterface) {
                $this->htmlParts[] = $block->toHtml();
            } else {
                $this->messageManager->addErrorMessage('Block with name "'.$blockName.'" not found');
            }
        }
    }

    private function getTargetBlockNames(string $blockName): array
    {
        $blockNames = [
            $blockName,
            'loki-components.messages',
            'loki-components.debugger',
        ]; // @todo: Another way to add default blocks to this list

        /** @var Http $request */
        $request = $this->request;
        $targets = $request->getHeader('X-Alpine-Target');
        if (!empty($targets)) {
            $targets = explode(' ', $targets);
            $blockNames = array_merge($blockNames, $this->convertTargetsToBlockNames($targets));
        }

        $targets = (string)$this->request->getParam('target');
        if (!empty($targets)) {
            $targets = explode(' ', $targets);
            $blockNames = array_merge($blockNames, $this->convertTargetsToBlockNames($targets));
        }

        $blockNames = array_unique($blockNames);

        $this->debugger->add('targetBlocks', $blockNames);

        return $blockNames;
    }

    private function convertTargetsToBlockNames(array $targets): array
    {
        $blockNames = [];
        foreach ($targets as $target) {
            $blockNames[] = $this->componentRegistry->getComponentNameFromDomId($target);
        }

        return $blockNames;
    }

    private function getLayout(): LayoutInterface
    {
        $layout = $this->layoutFactory->create();

        $handles = explode(' ', (string)$this->request->getParam('handles'));
        if (!empty($handles)) {
            foreach ($handles as $handle) {
                // @todo: Filter handle value
                $layout->getUpdate()->addHandle($handle);
            }
        }

        $layout->generateElements();

        return $layout;
    }

    private function getHtmlResult(): HtmlResult
    {
        $html = '';
        foreach ($this->htmlParts as $htmlPart) {
            $html .= $htmlPart;
        }

        /** @var HtmlResult $htmlResult */
        $htmlResult = $this->htmlResultFactory->create();
        $htmlResult->setContents($html);

        return $htmlResult;
    }
}
