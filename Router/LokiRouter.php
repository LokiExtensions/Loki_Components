<?php declare(strict_types=1);

namespace Loki\Components\Router;

use Loki\Components\Controller\Index\Html;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class LokiRouter implements RouterInterface
{
    public function __construct(
        private readonly ActionFactory $actionFactory
    ) {
    }

    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if (false === str_starts_with($identifier, 'loki_components/index/html')) {
            return null;
        }

        return $this->actionFactory->create(Html::class);
    }
}
