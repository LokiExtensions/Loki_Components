<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Index;

use Loki\Components\Controller\Index\Polling;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class PollingTest extends TestCase
{
    public function testReturnsEmptyJsonWithoutPollingHandlers(): void
    {
        $controller = ObjectManager::getInstance()->create(Polling::class);

        $response = ObjectManager::getInstance()->create(HttpResponse::class);
        $controller->execute()->renderResult($response);

        $this->assertSame('[]', $response->getBody());
    }
}
