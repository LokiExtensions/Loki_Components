<?php

declare(strict_types=1);

namespace Loki\Components\Controller\Index;

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
use Loki\Components\Config\Config;
use Loki\Components\Controller\HtmlResult;
use Loki\Components\Controller\HtmlResultFactory;
use Loki\Components\Exception\NoBlockFoundException;
use Loki\Components\Exception\RedirectException;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Controller\RepositoryDispatcher;
use Loki\Components\Util\Controller\RequestDataLoader;
use Loki\Components\Util\Controller\TargetRenderer;

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
            throw new NoBlockFoundException('Block with name "'.$blockName.'" is not found');
        }

        return $block;
    }

    private function getHtmlResult(array $htmlParts = []): HtmlResult
    {
        $html = '';
        foreach ($htmlParts as $htmlPart) {
            $htmlPart = preg_replace("/([\r\n]+)/", "\n", $htmlPart);
            $html .= $htmlPart."\n\n";
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
        $this->globalMessageRegistry->toSession();

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
            $errorWrapper = '<details>';
            $errorWrapper .= '<summary>'.$error.'</summary>';
            $errorWrapper .= '<br/> ['.$exception->getFile().' line '.$exception->getLine().'] <br/>';
            $errorWrapper .= '<small>'.nl2br($exception->getTraceAsString()).'</small>';
            $errorWrapper .= '</details>';
            $error = $errorWrapper;
        }

        $this->globalMessageRegistry->addError($error);
    }
}
