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
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\ComponentRepositoryInterface;
use Yireo\LokiComponents\Controller\HtmlResult;
use Yireo\LokiComponents\Controller\HtmlResultFactory;
use Yireo\LokiComponents\Util\Debugger;

class Html implements HttpPostActionInterface, HttpGetActionInterface
{
    private array $htmlParts = [];

    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly RequestInterface $request,
        private readonly ComponentRegistry $componentRegistry,
        private readonly MessageManager $messageManager,
        private readonly Debugger $debugger
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $data = json_decode($this->request->getContent(), true);

        $this->sanityCheck($data);
        $this->initializeLayout($data['handles']);

        $block = $this->getBlock($data['block']);
        $this->saveDataToComponent($block, $data['componentData']);

        $this->renderBlocks($this->getTargetBlockNames($data['targets']));

        return $this->getHtmlResult();
    }

    private function getBlock(string $blockName): AbstractBlock
    {
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

    private function sanityCheck(array $requestData): void
    {
        /** @var Http $request */
        $request = $this->request;
        if ($request->getHeader('X-Alpine-Request') !== 'true') {
            throw new RuntimeException('Not an Alpine request');
        }

        $requiredFields = ['targets', 'componentData', 'block', 'handles'];
        foreach ($requiredFields as $requiredField) {
            if (false === array_key_exists($requiredField, $requestData)) {
                throw new RuntimeException('No '.$requiredField.' in request');
            }
        }
    }

    private function saveDataToComponent(AbstractBlock $block, mixed $componentData): void
    {
        try {
            $component = $this->componentRegistry->getComponentFromBlock($block);
        } catch (RuntimeException $e) {
            die($e->getMessage());
        }

        $this->debugComponent($component);

        $repository = $component->getRepository();
        if (false === $repository instanceof ComponentRepositoryInterface) {
            return;
        }

        try {
            $this->debug('data', $componentData);
            $repository->save($componentData);
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage()); // @todo: Or use GlobalMessageRegistry?
        }
    }

    private function debugComponent(ComponentInterface $component): void
    {
        $this->debug('component', $component->getName());

        $viewModel = $component->getViewModel();
        if (is_object($viewModel)) {
            $this->debug('viewModel', get_class($viewModel));
        }

        $repository = $component->getRepository();
        if (is_object($repository)) {
            $this->debug('repository', get_class($repository));
        }

        $this->debug('validators', $component->getValidators());
        $this->debug('filters', $component->getFilters());

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

    private function getTargetBlockNames(array $targets): array
    {
        $blockNames = $this->convertTargetsToBlockNames($targets);
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

    private function initializeLayout(array $handles): void
    {
        if (!empty($handles)) {
            foreach ($handles as $handle) {
                $handle = preg_replace('/([^a-z0-9\-\_]+)/', '', $handle);
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
            $htmlPart = str_replace("\n\n", "\n", $htmlPart);
            $html .= $htmlPart."\n\n\n";
        }

        /** @var HtmlResult $htmlResult */
        $htmlResult = $this->htmlResultFactory->create();
        $htmlResult->setContents($html);

        return $htmlResult;
    }

    private function debug(string $name, $value): void
    {
        $this->debugger->add($name, $value);
    }
}
