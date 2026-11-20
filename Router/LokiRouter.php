<?php declare(strict_types=1);

namespace Loki\Components\Router;

use Loki\Components\Controller\Index\Html;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class LokiRouter implements RouterInterface
{
    private const PATH = 'loki_components/index/html';

    public function __construct(
        private readonly ActionFactory $actionFactory
    ) {
    }

    public function match(RequestInterface $request): ActionInterface|null
    {
        /** @var HttpRequest $request */
        $identifier = trim((string)$request->getPathInfo(), '/');
        if ($identifier !== self::PATH && false === str_starts_with($identifier, self::PATH.'/')) {
            return null;
        }

        return $this->actionFactory->create(Html::class);
    }
}
