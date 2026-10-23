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
        $requestData = $this->getEmptyRequestData();

        $signData = [
            'handles' => $requestData['handles'] ?? [],
            'pageHandles' => $requestData['pageHandles'] ?? [],
            'request' => $requestData['request'] ?? [],
        ];

        $requestData['signature'] = $this->getAjaxSignature()->sign($signData);
        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $data = $requestDataLoader->load();
        $this->assertNotEmpty($data);
    }

    public function testLoadWithTamperedPayload()
    {
        $requestData = $this->getEmptyRequestData();

        $signData = [
            'handles' => $requestData['handles'] ?? [],
            'pageHandles' => $requestData['pageHandles'] ?? [],
            'request' => $requestData['request'] ?? [],
        ];

        $requestData['signature'] = $this->getAjaxSignature()->sign($signData);

        $requestData['handles'] = ['tampered_handle'];

        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payload was tampered with');
        $requestDataLoader->load();
    }

    public function testLoadWithMissingSignature()
    {
        $requestData = $this->getEmptyRequestData();

        $requestDataLoader = $this->getRequestDataLoader($requestData, $this->getValidHeaders());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payload was tampered with');
        $requestDataLoader->load();
    }

    public function testLoadWithMultipartPayload()
    {
        $httpRequest = $this->getHttpRequest();
        $requestDataLoader = $this->getRequestDataLoader([], $this->getValidHeaders(), $httpRequest);
        $httpRequest->setContent('');
        $httpRequest->setPostValue('payload', json_encode($this->getSignedRequestData([])));

        $data = $requestDataLoader->load();
        $this->assertSame([], $data['updates']);
    }

    public function testLoadWithTamperedMultipartPayload()
    {
        $requestData = $this->getSignedRequestData([]);
        $requestData['handles'] = ['tampered_handle'];

        $httpRequest = $this->getHttpRequest();
        $requestDataLoader = $this->getRequestDataLoader([], $this->getValidHeaders(), $httpRequest);
        $httpRequest->setContent('');
        $httpRequest->setPostValue('payload', json_encode($requestData));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payload was tampered with');
        $requestDataLoader->load();
    }

    public function testLoadWithInvalidMultipartPayload()
    {
        $httpRequest = $this->getHttpRequest();
        $requestDataLoader = $this->getRequestDataLoader([], $this->getValidHeaders(), $httpRequest);
        $httpRequest->setContent('');
        $httpRequest->setPostValue('payload', '"just a string"');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not a valid request');
        $requestDataLoader->load();
    }

    public function testMergeRequestParamsUsesOriginalRequestUri()
    {
        $httpRequest = $this->getHttpRequest();
        $requestDataLoader = $this->getRequestDataLoader(
            $this->getSignedRequestData(['request_uri' => '/foo/bar?baz=1']),
            $this->getValidHeaders(),
            $httpRequest
        );

        $requestDataLoader->mergeRequestParams();

        $this->assertSame('/foo/bar?baz=1', $httpRequest->getRequestUri());
        $this->assertSame('/foo/bar?baz=1', $httpRequest->getParam('currentUri'));
    }

    public function testMergeRequestParamsKeepsRequestUriWhenOriginalIsEmpty()
    {
        $httpRequest = $this->getHttpRequest();
        $httpRequest->setRequestUri('/loki_components/index/html');
        $requestDataLoader = $this->getRequestDataLoader(
            $this->getSignedRequestData(['request_uri' => '', 'route_name' => 'cms']),
            $this->getValidHeaders(),
            $httpRequest
        );

        $requestDataLoader->mergeRequestParams();

        $this->assertSame('/loki_components/index/html', $httpRequest->getRequestUri());
    }

    private function getSignedRequestData(array $request): array
    {
        $requestData = $this->getEmptyRequestData();
        $requestData['request'] = $request;
        $requestData['signature'] = $this->getAjaxSignature()->sign([
            'handles' => $requestData['handles'],
            'pageHandles' => $requestData['pageHandles'],
            'request' => $requestData['request'],
        ]);

        return $requestData;
    }

    private function getHttpRequest(): HttpRequest
    {
        return ObjectManager::getInstance()->create(HttpRequest::class);
    }

    private function getRequestDataLoader(
        array $data = [],
        array $headerArray = [],
        ?HttpRequest $httpRequest = null
    ): RequestDataLoader {
        $httpRequest = $httpRequest ?? $this->getHttpRequest();

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

    private function getEmptyRequestData(): array
    {
        return [
            'targets' => [],
            'handles' => [],
            'updates' => [],
            'request' => [],
            'pageHandles' => [],
        ];
    }
}
