<?php

declare(strict_types=1);

namespace Yireo\LokiComponents\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;
use RuntimeException;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Controller\HtmlResult;
use Yireo\LokiComponents\Controller\HtmlResultFactory;

class Html implements HttpPostActionInterface, HttpGetActionInterface
{
    private array $htmlParts = [];

    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly RequestInterface $request,
        private readonly ComponentRegistry $componentRegistry,
        private readonly MessageManager $messageManager,
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $this->securityCheck();
        $this->initializeLayout();

        $block = $this->getBlock();
        $this->saveDataToComponent($block);

        $this->renderBlocks($this->getTargetBlockNames($block->getNameInLayout()));

        return $this->getHtmlResult();
    }

    private function getBlock(): AbstractBlock
    {
        $blockName = $this->request->getParam('block');
        if (empty($blockName)) {
            throw new RuntimeException('Block name not specified');
        }

        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new RuntimeException('Block with name "'.$blockName.'" is not found');
        }

        $this->debug('block', $blockName);

        return $block;
    }

    private function securityCheck(): void
    {
        /** @var Http $request */
        $request = $this->request;
        if ($request->getHeader('X-Alpine-Request') !== 'true') {
            throw new RuntimeException('Not an Alpine request');
        }
    }

    private function saveDataToComponent(AbstractBlock $block): void
    {
        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (RuntimeException $e) {
            die($e->getMessage());
        }

        $repository = $component->getRepository();
        $this->debug('repository', get_class($repository));

        try {
            $data = $this->getRequestData();
            $this->debug('mutator data', $data);
            $repository->save($data);
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    private function getRequestData(): mixed
    {
        $json = $this->request->getParam('json');
        if ($json) {
            return json_decode($json, true);
        }

        return $this->request->getParam('data');
    }

    private function renderBlocks(array $blockNames): void
    {
        foreach ($blockNames as $blockName) {
            $block = $this->layout->getBlock($blockName);
            if ($block instanceof BlockInterface) {
                $this->htmlParts[] = $block->toHtml();
            } else {
                $this->messageManager->addErrorMessage('Block with name "'.$blockName.'" not found');
            }
        }
    }

    private function getTargetBlockNames(string $blockName): array
    {
        $blockNames = [$blockName,];

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

        $this->debug('targetBlocks', $blockNames);

        return $blockNames;
    }

    private function convertTargetsToBlockNames(array $targets): array
    {
        $blockNames = [];
        foreach ($targets as $target) {
            $blockNames[] = $this->componentRegistry->getBlockNameFromElementId($target);
        }

        return $blockNames;
    }

    private function initializeLayout(): void
    {
        $handles = explode(' ', (string)$this->request->getParam('handles'));
        if (!empty($handles)) {
            foreach ($handles as $handle) {
                // @todo: Filter handle value
                $this->layout->getUpdate()->addHandle($handle);
            }
        }

        $this->layout->generateElements();
    }

    private function getHtmlResult(): HtmlResult
    {
        $html = '';
        foreach ($this->htmlParts as $htmlPart) {
            $html .= $htmlPart."\n\n";
        }

        /** @var HtmlResult $htmlResult */
        $htmlResult = $this->htmlResultFactory->create();
        $htmlResult->setContents($html);

        return $htmlResult;
    }

    private function debug(string $name, $value): void
    {
        return;
        $debugger = $this->componentRegistry->getComponentByName('debugger');
        $debugger->getViewModel()->add($name, $value);
    }
}
