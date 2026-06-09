<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Laminas\Http\Headers;
use Loki\Components\Util\Controller\RequestDataLoader;
use Loki\Components\Util\Security\AjaxSignature;
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
            'pageHandles' => [],
        ];

        $requestData['signature'] = $this->getAjaxSignature()->sign(
            $requestData['handles'],
            $requestData['pageHandles'],
            $requestData['request']
        );

        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $data = $requestDataLoader->load();
        $this->assertNotEmpty($data);
    }

    public function testLoadWithTamperedPayload()
    {
        $requestData = [
            'targets' => [],
            'handles' => [],
            'updates' => [],
            'request' => [],
            'pageHandles' => [],
        ];

        $requestData['signature'] = $this->getAjaxSignature()->sign(
            $requestData['handles'],
            $requestData['pageHandles'],
            $requestData['request']
        );

        $requestData['handles'] = ['tampered_handle'];

        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payload was tampered with');
        $requestDataLoader->load();
    }

    public function testLoadWithMissingSignature()
    {
        $requestData = [
            'targets' => [],
            'handles' => [],
            'updates' => [],
            'request' => [],
            'pageHandles' => [],
        ];

        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payload was tampered with');
        $requestDataLoader->load();
    }

    private function getRequestDataLoader(array $data = [], array $headerArray = []): RequestDataLoader
    {
        $httpRequest = ObjectManager::getInstance()->create(HttpRequest::class);

        $headers = new Headers();
        $headers->addHeaders($headerArray);

        $httpRequest->setHeaders($headers);
        $httpRequest->setContent(json_encode($data));

        $ajax = ObjectManager::getInstance()->create(Ajax::class, ['httpRequest' => $httpRequest]);
        return new RequestDataLoader($httpRequest, $ajax, $this->getAjaxSignature());
    }

    private function getAjaxSignature(): AjaxSignature
    {
        return ObjectManager::getInstance()->get(AjaxSignature::class);
    }

    private function getValidHeaders(): array
    {
        return ['X-Alpine-Request' => 'true'];
    }
}
