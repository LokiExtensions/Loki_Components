<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Adminhtml\Index;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\TestCase\AbstractBackendController;

class HtmlDispatchTest extends AbstractBackendController
{
    private const TARGET = 'loki-components.utils.local-messages';
    private const TARGET_MARKER = 'loki-components-messages';

    protected $uri = 'backend/loki_components/index/html';
    protected $resource = 'Magento_Backend::admin';
    protected $httpMethod = 'POST';

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRequest();
    }

    public function testRendersTargetsForAdminUser(): void
    {
        $this->dispatch($this->uri);

        $this->assertStringContainsString(self::TARGET_MARKER, (string)$this->getResponse()->getBody());
    }

    public function testDoesNotRenderTargetsWithoutAdminLogin(): void
    {
        $this->_auth->logout();
        $this->dispatch($this->uri);

        $this->assertStringNotContainsString(self::TARGET_MARKER, (string)$this->getResponse()->getBody());
    }

    private function prepareRequest(): void
    {
        $payload = [
            'targets' => [self::TARGET],
            'handles' => ['loki_messages'],
            'pageHandles' => [],
            'updates' => [],
            'request' => [],
        ];

        $payload['signature'] = $this->_objectManager->get(AjaxSignature::class)->sign([
            'handles' => $payload['handles'],
            'pageHandles' => $payload['pageHandles'],
            'request' => $payload['request'],
        ]);

        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));

        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $request->setHeaders($headers);
        $request->setMethod($this->httpMethod);
        $request->setContent((string)json_encode($payload));
    }
}
