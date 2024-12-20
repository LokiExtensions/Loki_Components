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
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\Hydrator;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\MutatorInterface;
use Yireo\LokiComponents\Controller\HtmlResult;
use Yireo\LokiComponents\Controller\HtmlResultFactory;
use Yireo\LokiComponents\ViewModel\Debugger;

class Html implements HttpPostActionInterface, HttpGetActionInterface
{
    private array $htmlParts = [];

    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly RequestInterface $request,
        private readonly ComponentRegistry $componentRegistry,
        private readonly MessageManager $messageManager,
        private readonly Debugger $debugger,
        private readonly Hydrator $hydrator,
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $this->securityCheck();
        $this->initializeLayout();

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
            $componentDefinition = $this->componentRegistry->getComponentDefinitionFromBlockName($blockName);
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $viewModel = $componentDefinition->getViewModel();
        if ($viewModel instanceof ComponentInterface) {
            $block = $this->layout->getBlock($blockName);
            if ($block instanceof AbstractBlock) {
                $this->hydrator->hydrate($block, $viewModel);
            }
        }

        $mutator = $componentDefinition->getMutator();
        if (false === $mutator instanceof MutatorInterface) {
            return;
        }

        $this->debugger->add('mutator', get_class($mutator));

        try {
            // @todo: Possibly sanitize values first?
            $data = $this->request->getParam('data');
            $data = json_decode($data, true);
            $this->debugger->add('mutator data', $data);

            $mutator->mutate($data);
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
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
            $html .= $htmlPart;
        }

        /** @var HtmlResult $htmlResult */
        $htmlResult = $this->htmlResultFactory->create();
        $htmlResult->setContents($html);

        return $htmlResult;
    }
}
