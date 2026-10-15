<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Router;

use Loki\Components\Controller\Index\Html;
use Loki\Components\Router\LokiRouter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RouterList;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class LokiRouterTest extends TestCase
{
    public function testRouterIsRegisteredInFrontendBeforeStandardRouter(): void
    {
        $routerClasses = [];
        foreach (ObjectManager::getInstance()->create(RouterList::class) as $router) {
            $routerClasses[] = $router::class;
        }

        $routerClasses = array_map(fn (string $class) => str_replace('\\Interceptor', '', $class), $routerClasses);
        $this->assertContains(LokiRouter::class, $routerClasses);

        $standardRouterPosition = array_search(\Magento\Framework\App\Router\Base::class, $routerClasses, true);
        if ($standardRouterPosition !== false) {
            $this->assertLessThan($standardRouterPosition, array_search(LokiRouter::class, $routerClasses, true));
        }
    }

    public function testMatchesHtmlEndpointWithAdditionalPath(): void
    {
        $request = ObjectManager::getInstance()->create(HttpRequest::class);
        $request->setPathInfo('/loki_components/index/html/foo/bar');

        $action = ObjectManager::getInstance()->get(LokiRouter::class)->match($request);
        $this->assertInstanceOf(Html::class, $action);
    }
}
