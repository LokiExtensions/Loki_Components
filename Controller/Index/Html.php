<?php

declare(strict_types=1);

namespace Yireo\LokiComponents\Controller\Index;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;
use RuntimeException;
use Yireo\LokiComponents\Component\ComponentRegistry;
use Yireo\LokiComponents\Component\ComponentRepositoryInterface;
use Yireo\LokiComponents\Controller\HtmlResult;
use Yireo\LokiComponents\Controller\HtmlResultFactory;
use Yireo\LokiComponents\Exception\NoComponentFoundException;
use Yireo\LokiComponents\Messages\GlobalMessageRegistry;

class Html implements HttpPostActionInterface, HttpGetActionInterface
{
    private array $htmlParts = [];

    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly RequestInterface $request,
        private readonly ComponentRegistry $componentRegistry,
        private readonly GlobalMessageRegistry $globalMessageRegistry,
        private readonly EventManager $eventManager,
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $data = json_decode($this->request->getContent(), true);

        $this->sanityCheck($data);
        $this->initializeLayout($data['handles']);

        try {
            $block = $this->getBlock($data['block']);
            $this->saveDataToComponent($block, $data['componentData']);
        } catch(Exception $exception) {
            $this->globalMessageRegistry->addError($exception->getMessage());
            return $this->getHtmlResult();
        }

        $this->renderBlocks($this->getTargetBlockNames($data['targets']));

        return $this->getHtmlResult();
    }

    private function getBlock(string $blockName): AbstractBlock
    {
        if (empty($blockName)) {
            throw new NoComponentFoundException('Block name not specified');
        }

        $block = $this->layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new NoComponentFoundException('Block with name "'.$blockName.'" is not found');
        }

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
        $component = $this->componentRegistry->getComponentFromBlock($block);
        $this->eventManager->dispatch('loki_components_repository', ['component' => $component]);

        $repository = $component->getRepository();
        if (false === $repository instanceof ComponentRepositoryInterface) {
            return;
        }

        $repository->save($componentData);
    }

    private function renderBlocks(array $blockNames): void
    {
        foreach ($blockNames as $blockName) {
            $block = $this->layout->getBlock($blockName);
            if ($block instanceof BlockInterface) {
                $this->htmlParts[] = $block->toHtml();
            } else {
                $this->globalMessageRegistry->addError('Block with name "'.$blockName.'" not found');
            }
        }
    }

    private function getTargetBlockNames(array $targets): array
    {
        $blockNames = $this->convertTargetsToBlockNames($targets);
        $blockNames = array_unique($blockNames);
        $this->eventManager->dispatch('loki_components_blocks', ['blocks' => $blockNames]);

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
}
