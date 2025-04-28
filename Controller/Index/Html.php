<?php

declare(strict_types=1);

namespace Yireo\LokiComponents\Controller\Index;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Yireo\LokiComponents\Config\Config;
use Yireo\LokiComponents\Controller\HtmlResult;
use Yireo\LokiComponents\Controller\HtmlResultFactory;
use Yireo\LokiComponents\Exception\NoBlockFoundException;
use Yireo\LokiComponents\Exception\RedirectException;
use Yireo\LokiComponents\Messages\GlobalMessageRegistry;
use Yireo\LokiComponents\Util\Controller\LayoutLoader;
use Yireo\LokiComponents\Util\Controller\RepositoryDispatcher;
use Yireo\LokiComponents\Util\Controller\RequestDataLoader;
use Yireo\LokiComponents\Util\Controller\TargetRenderer;

class Html implements HttpPostActionInterface, HttpGetActionInterface
{
    public function __construct(
        private readonly LayoutLoader $layoutLoader,
        private readonly RequestDataLoader $requestDataLoader,
        private readonly RepositoryDispatcher $repositoryDispatcher,
        private readonly TargetRenderer $targetRenderer,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly JsonResultFactory $jsonResultFactory,
        private readonly GlobalMessageRegistry $globalMessageRegistry,
        private readonly Config $config,
        private readonly AppState $appState,
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $data = $this->requestDataLoader->load();
        $this->requestDataLoader->mergeRequestParams();
        $layout = $this->layoutLoader->load($data['handles']);

        try {
            $this->repositoryDispatcher->dispatch(
                $this->getBlock($layout, $data['block']),
                $data['componentData']
            );
        } catch (NoBlockFoundException $exception) {
            // @todo: Write this to a dedicated log for debugging purpose

        } catch (RedirectException $redirectException) {
            return $this->getJsonRedirect($redirectException->getMessage());

        } catch (Exception $exception) {
            // @phpcs:ignore
            echo get_class($exception) . ': ' . $exception->getMessage() . "\n\n";
            $this->addGlobalException($exception);
        }

        if ($this->allowRendering($data)) {
            try {
                $htmlParts = $this->targetRenderer->render($layout, $data['targets']);
            } catch (RedirectException $redirectException) {
                return $this->getJsonRedirect($redirectException->getMessage());
            }

        } else {
            $htmlParts = [];
        }

        return $this->getHtmlResult($htmlParts);
    }

    private function getBlock(LayoutInterface $layout, string $blockName): AbstractBlock
    {
        if ($blockName === '' || $blockName === '0') {
            throw new NoBlockFoundException('Block name not specified');
        }

        $block = $layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new NoBlockFoundException('Block with name "' . $blockName . '" is not found');
        }

        return $block;
    }

    private function getHtmlResult(array $htmlParts = []): HtmlResult
    {
        $html = '';
        foreach ($htmlParts as $htmlPart) {
            $htmlPart = preg_replace("/([\r\n]+)/", "\n", $htmlPart);
            $html .= $htmlPart . "\n\n";
        }

        $htmlResult = $this->htmlResultFactory->create();
        $htmlResult->setContents($html);

        return $htmlResult;
    }

    private function getJsonRedirect(string $redirectUrl): JsonResult
    {
        $json = $this->jsonResultFactory->create()->setData([
            'redirect' => $redirectUrl,
        ]);

        $json->setHeader('X-Loki-Redirect', $redirectUrl);

        return $json;
    }

    private function allowRendering(array $data): bool
    {
        return !isset($data['componentData']['render']) || $data['componentData']['render'] === 1;
    }

    private function addGlobalException(Exception $exception): void
    {
        $error = $exception->getMessage();

        if ($this->config->isDebug() && $this->appState->getMode() === AppState::MODE_DEVELOPER) {
            $error .= '<br/> [' . $exception->getFile() . ' line ' . $exception->getLine() . '] <br/>';
            $error .= $exception->getTraceAsString();
        }

        $this->globalMessageRegistry->addError($error);
    }
}
