<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Laminas\Http\Headers;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Loki\Components\Util\Ajax;

class RequestDataLoaderTest extends TestCase
{
    public function testLoadWithoutValidAjaxHeader()
    {
        $requestDataLoader = $this->getRequestDataLoader();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not a valid request');
        $requestDataLoader->load();
    }

    public function testLoadWithoutValidInput()
    {
        $requestData = [];
        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not a valid request');
        $requestDataLoader->load();
    }

    public function testLoadWithValidData()
    {
        $requestData = [
            'targets' => [],
            'handles' => [],
            'updates' => [],
            'request' => [],
        ];

        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $data = $requestDataLoader->load();
        $this->assertNotEmpty($data);
    }

    private function getRequestDataLoader(array $data = [], array $headerArray = []): RequestDataLoader
    {
        $httpRequest = ObjectManager::getInstance()->create(HttpRequest::class);

        $headers = new Headers();
        $headers->addHeaders($headerArray);

        $httpRequest->setHeaders($headers);
        $httpRequest->setContent(json_encode($data));

        $ajax = ObjectManager::getInstance()->create(Ajax::class, ['httpRequest' => $httpRequest]);
        return new RequestDataLoader($httpRequest, $ajax);
    }

    private function getValidHeaders(): array
    {
        return ['X-Alpine-Request' => 'true'];
    }
}
