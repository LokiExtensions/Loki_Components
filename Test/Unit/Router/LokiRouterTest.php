<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Router;

use Loki\Components\Controller\Index\Html;
use Loki\Components\Router\LokiRouter;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LokiRouterTest extends TestCase
{
    #[DataProvider('getMatchingPaths')]
    public function testMatchesHtmlEndpoint(string $pathInfo): void
    {
        $action = $this->createMock(ActionInterface::class);
        $actionFactory = $this->createMock(ActionFactory::class);
        $actionFactory->expects($this->once())->method('create')->with(Html::class)->willReturn($action);

        $router = new LokiRouter($actionFactory);
        $this->assertSame($action, $router->match($this->getRequest($pathInfo)));
    }

    #[DataProvider('getNonMatchingPaths')]
    public function testDoesNotMatchOtherPaths(string $pathInfo): void
    {
        $actionFactory = $this->createMock(ActionFactory::class);
        $actionFactory->expects($this->never())->method('create');

        $router = new LokiRouter($actionFactory);
        $this->assertNull($router->match($this->getRequest($pathInfo)));
    }

    public static function getMatchingPaths(): array
    {
        return [
            ['/loki_components/index/html'],
            ['/loki_components/index/html/'],
            ['/loki_components/index/html/form_key/abc'],
        ];
    }

    public static function getNonMatchingPaths(): array
    {
        return [
            ['/'],
            ['/loki_components/index/polling'],
            ['/checkout/loki_components/index/html'],
            ['/cms/page/view'],
        ];
    }

    private function getRequest(string $pathInfo): HttpRequest
    {
        $request = $this->createMock(HttpRequest::class);
        $request->method('getPathInfo')->willReturn($pathInfo);

        return $request;
    }
}
