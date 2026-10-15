<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Index;

use Loki\Components\Test\Integration\LokiComponentsTestCase;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;

#[AppArea('frontend')]
class HtmlDispatchTest extends LokiComponentsTestCase
{
    private const HANDLE = 'loki_components_index_test';
    private const TARGET = 'loki-components.test.dummy1';

    #[AppIsolation(true)]
    public function testValidRequestRendersTargets(): void
    {
        $this->dispatchHtmlRequest($this->buildSignedBody());

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode() ?? 200);
        $this->assertStringStartsWith('<html>', $this->getResponseBody());
        $this->assertStringOccursOnPage('>'.self::TARGET.'</div>');
        $this->assertElementIdExistsOnPage('loki-components-test-dummy1');
    }

    #[AppIsolation(true)]
    public function testRequestWithInvalidFormKeyIsRejected(): void
    {
        $this->getRequest()->setPostValue('form_key', 'invalid-form-key');
        $this->dispatchHtmlRequest($this->buildSignedBody());

        $this->assertSame(400, $this->getResponse()->getHttpResponseCode());
        $this->assertStringOccursOnPage('Invalid Form Key. Please refresh the page');
        $this->assertStringNotOccursOnPage(self::TARGET);
    }

    #[AppIsolation(true)]
    public function testGetRequestIsNotAllowed(): void
    {
        $this->setAsAjaxRequest();
        $this->getRequest()->setMethod('GET');
        $this->dispatch('loki_components/index/html');

        $this->assertStringNotOccursOnPage(self::TARGET);
        $this->assertNotSame(200, $this->getResponse()->getHttpResponseCode() ?? 200);
    }

    private function dispatchHtmlRequest(array $body): void
    {
        $this->setAsAjaxRequest();

        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setContent((string)json_encode($body));

        $this->dispatch('loki_components/index/html');
    }

    private function buildSignedBody(): array
    {
        $body = [
            'updates' => [],
            'targets' => [self::TARGET],
            'handles' => [self::HANDLE],
            'pageHandles' => ['1column'],
            'request' => [],
        ];

        $body['signature'] = $this->getObjectManager()->get(AjaxSignature::class)->sign([
            'handles' => $body['handles'],
            'pageHandles' => $body['pageHandles'],
            'request' => $body['request'],
        ]);

        return $body;
    }
}
