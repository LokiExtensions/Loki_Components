<?php

declare(strict_types=1);

namespace Loki\Components\Controller\Adminhtml\Index;

use Exception;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Util\Controller\ComponentUpdate;
use Loki\Components\Util\Controller\ComponentUpdateFactory;
use Loki\Components\ViewModel\Messages;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Loki\Components\Config\Config;
use Loki\Components\Controller\HtmlResult;
use Loki\Components\Controller\HtmlResultFactory;
use Loki\Components\Exception\NoBlockFoundException;
use Loki\Components\Exception\NoComponentFoundException;
use Loki\Components\Exception\RedirectException;
use Loki\Components\Util\Controller\AllowedTargets;
use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Controller\RepositoryDispatcher;
use Loki\Components\Util\Controller\RenderFlag;
use Loki\Components\Util\Controller\RequestDataLoader;
use Loki\Components\Util\Controller\TargetRenderer;
use Psr\Log\LoggerInterface;

class Html extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public const ADMIN_RESOURCE = 'Loki_Components::index';

    public function __construct(
        Context $context,
        private readonly LayoutLoader $layoutLoader,
        private readonly RequestDataLoader $requestDataLoader,
        private readonly RepositoryDispatcher $repositoryDispatcher,
        private readonly TargetRenderer $targetRenderer,
        private readonly HtmlResultFactory $htmlResultFactory,
        private readonly JsonResultFactory $jsonResultFactory,
        private readonly Config $config,
        private readonly AppState $appState,
        private readonly LoggerInterface $logger,
        private readonly ComponentRegistry $componentRegistry,
        private readonly AuthorizationInterface $authorization,
        private readonly Messages $messages,
        private readonly ComponentUpdateFactory $componentUpdateFactory,
        private readonly RenderFlag $renderFlag,
        private readonly AllowedTargets $allowedTargets
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $data = $this->requestDataLoader->load();
        $this->requestDataLoader->mergeRequestParams();
        $layout = $this->layoutLoader->load($data['handles'], $data['pageHandles'], true);

        $updates = $this->enrichUpdates($data['updates'], $layout);
        $updates = $this->sortUpdates($updates);

        foreach ($updates as $update) {
            if (false === $this->authorization->isAllowed($this->getAclResource($update))) {
                return $this->getAccessDeniedResult();
            }
        }

        foreach ($updates as $update) {
            try {
                $this->repositoryDispatcher->dispatch(
                    $update['component'],
                    $update['update'] ?? null
                );
            } catch (RedirectException $redirectException) {
                return $this->getJsonRedirect($redirectException);

            } catch (Exception $exception) {
                $this->logger->critical($exception);
                $this->addGlobalException($exception);
            }
        }

        $this->messages->syncMessages();

        $componentUpdates = $this->getComponentUpdates($updates);

        try {
            $this->repositoryDispatcher->postDispatch($layout, $componentUpdates);
        } catch (RedirectException $redirectException) {
            return $this->getJsonRedirect($redirectException);
        }

        if ($this->allowRendering($data)) {
            try {
                $htmlParts = $this->targetRenderer->render(
                    $layout,
                    $this->allowedTargets->filter($data['targets'], $componentUpdates)
                );
            } catch (RedirectException $redirectException) {
                return $this->getJsonRedirect($redirectException);
            }

        } else {
            $htmlParts = [];
        }

        return $this->getHtmlResult($htmlParts);
    }

    private function enrichUpdates(array $updates, LayoutInterface $layout): array
    {
        $enrichedUpdates = [];
        foreach ($updates as $update) {
            if (!isset($update['blockName']) || !is_string($update['blockName'])) {
                continue;
            }

            try {
                $update['block'] = $this->getBlock($layout, $update['blockName']);
                $update['component'] = $this->componentRegistry->getComponentFromBlock($update['block']);
            } catch (NoBlockFoundException|NoComponentFoundException $exception) {
                $this->logger->critical($exception);
                continue;
            }

            $enrichedUpdates[] = $update;
        }

        return $enrichedUpdates;
    }

    private function sortUpdates(array $updates): array
    {
        usort($updates, function (array $a, array $b) {
            /** @var ComponentInterface $componentA */
            $componentA = $a['component'];
            /** @var ComponentInterface $componentB */
            $componentB = $b['component'];

            return $componentA->getRepository()?->getPriority() <=> $componentB->getRepository()?->getPriority();
        });

        return $updates;
    }

    private function getBlock(LayoutInterface $layout, string $blockName): AbstractBlock
    {
        if ($blockName === '' || $blockName === '0') {
            throw new NoBlockFoundException('Block name not specified');
        }

        $block = $layout->getBlock($blockName);
        if (false === $block instanceof AbstractBlock) {
            throw new NoBlockFoundException(
                'Block with name "' . $blockName . '" is not found'
            );
        }

        return $block;
    }

    private function getHtmlResult(array $htmlParts = []): HtmlResult
    {
        $html = '';
        foreach ($htmlParts as $htmlPart) {
            $htmlPart = preg_replace("/\r\n|\r/", "\n", $htmlPart);
            $html .= $htmlPart . "\n\n";
        }

        $htmlResult = $this->htmlResultFactory->create();
        $htmlResult->setContents($html);

        return $htmlResult;
    }

    private function getJsonRedirect(RedirectException $redirectException): JsonResult
    {
        $message = $redirectException->getMessage();
        if ($message) {
            $this->messageManager->addErrorMessage($message);
        }

        $json = $this->jsonResultFactory->create()->setData([
            'redirect' => $redirectException->getUrl(),
        ]);

        $json->setHeader('X-Loki-Redirect', $redirectException->getUrl());

        return $json;
    }

    private function allowRendering(array $data): bool
    {
        return $this->renderFlag->allowRendering((array)$data['updates']);
    }

    /**
     * @return ComponentUpdate[]
     */
    private function getComponentUpdates(array $updates): array
    {
        $componentUpdates = [];
        foreach ($updates as $update) {
            $componentUpdates[] = $this->componentUpdateFactory->create([
                'block' => $update['block'],
                'component' => $update['component'],
                'componentData' => $update['update'] ?? null,
            ]);
        }

        return $componentUpdates;
    }

    private function getAclResource(array $update): string
    {
        $aclResource = $update['block']->getData('acl_resource');
        if (is_string($aclResource) && $aclResource !== '') {
            return $aclResource;
        }

        return self::ADMIN_RESOURCE;
    }

    private function getAccessDeniedResult(): JsonResult
    {
        $json = $this->jsonResultFactory->create();
        $json->setHttpResponseCode(403);
        $json->setData([
            'error' => true,
            'message' => (string)__('Access denied.'),
        ]);

        return $json;
    }

    private function addGlobalException(Exception $exception): void
    {
        $error = $exception->getMessage();

        if ($this->config->isDebug()
            && $this->appState->getMode() === AppState::MODE_DEVELOPER
        ) {
            $error .= '<br/> [' . $exception->getFile() . ' line '
                . $exception->getLine() . '] <br/>';
            $error .= $exception->getTraceAsString();
        }

        $this->messageManager->addErrorMessage($error);
    }

    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return new InvalidRequestException($this->getResponse());
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return $this->_processUrlKeys();
    }
}
