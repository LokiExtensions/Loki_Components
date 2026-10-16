<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Adminhtml\Index;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Controller\Adminhtml\Index\Html;
use Loki\Components\Test\Integration\Dummy\DummyRepository;
use Loki\Components\Test\Integration\Dummy\DummyRepositoryStorage;
use Loki\Components\Test\Integration\Traits\RegisterTestComponents;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class HtmlTest extends TestCase
{
    use RegisterTestComponents;

    private const BLOCK_NAME = 'loki-components.utils.local-messages';
    private const BLOCK_MARKER = 'loki-components-messages';
    private const ACL_RESOURCE = 'Loki_Components::index';

    protected function setUp(): void
    {
        $this->getStorage()->reset();
        $this->registerTestComponents([
            $this->createTestComponentDefinition(self::BLOCK_NAME, ['repository' => DummyRepository::class]),
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterTestComponents();
        $this->getStorage()->reset();
    }

    #[AppIsolation(true)]
    public function testDeniesUpdateWhenAclResourceIsNotAllowed(): void
    {
        $this->prepareRequest([['blockName' => self::BLOCK_NAME, 'update' => 'foo', 'aclResource' => self::ACL_RESOURCE]]);

        $this->expectException(AuthorizationException::class);
        try {
            $this->getController(false)->execute();
        } finally {
            $this->assertFalse($this->getStorage()->hasValue(self::BLOCK_NAME));
        }
    }

    #[AppIsolation(true)]
    public function testSavesUpdateWhenAclResourceIsAllowed(): void
    {
        $this->prepareRequest([['blockName' => self::BLOCK_NAME, 'update' => 'foo', 'aclResource' => self::ACL_RESOURCE]]);

        $result = $this->getController(true)->execute();
        $this->assertSame('foo', $this->getStorage()->getValue(self::BLOCK_NAME));

        $response = ObjectManager::getInstance()->create(HttpResponse::class);
        $result->renderResult($response);

        $body = (string)$response->getBody();
        $this->assertStringContainsString(self::BLOCK_MARKER, $body);
        $this->assertStringNotContainsString('<html>', $body, 'Admin output is not wrapped in an HTML document');
    }

    #[AppIsolation(true)]
    public function testDeniesUpdateWhenAclResourceInUpdateValueIsNotAllowed(): void
    {
        $this->prepareRequest([['blockName' => self::BLOCK_NAME, 'update' => ['foo' => 'bar', 'aclResource' => self::ACL_RESOURCE]]]);

        $this->expectException(AuthorizationException::class);
        try {
            $this->getController(false)->execute();
        } finally {
            $this->assertFalse($this->getStorage()->hasValue(self::BLOCK_NAME));
        }
    }

    #[AppIsolation(true)]
    public function testSavesUpdateWhenAclResourceInUpdateValueIsAllowed(): void
    {
        $update = ['foo' => 'bar', 'aclResource' => self::ACL_RESOURCE];
        $this->prepareRequest([['blockName' => self::BLOCK_NAME, 'update' => $update]]);

        $this->getController(true)->execute();
        $this->assertSame($update, $this->getStorage()->getValue(self::BLOCK_NAME));
    }

    #[AppIsolation(true)]
    public function testSkipsInvalidUpdates(): void
    {
        $this->prepareRequest([
            ['update' => 'no block name'],
            ['blockName' => 'loki-components.test.unknown', 'update' => 'unknown block'],
            ['blockName' => self::BLOCK_NAME, 'update' => 'foo'],
        ]);

        $this->getController(true)->execute();
        $this->assertSame('foo', $this->getStorage()->getValue(self::BLOCK_NAME));
    }

    private function getController(bool $isAllowed): Html
    {
        $authorization = $this->createMock(AuthorizationInterface::class);
        $authorization->method('isAllowed')->with(self::ACL_RESOURCE)->willReturn($isAllowed);

        return ObjectManager::getInstance()->create(Html::class, ['authorization' => $authorization]);
    }

    private function prepareRequest(array $updates): void
    {
        $payload = [
            'targets' => [self::BLOCK_NAME],
            'handles' => ['loki_messages'],
            'pageHandles' => [],
            'updates' => $updates,
            'request' => [],
        ];

        $payload['signature'] = ObjectManager::getInstance()->get(AjaxSignature::class)->sign([
            'handles' => $payload['handles'],
            'pageHandles' => $payload['pageHandles'],
            'request' => $payload['request'],
        ]);

        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));

        $request = ObjectManager::getInstance()->get(HttpRequest::class);
        $request->setHeaders($headers);
        $request->setContent((string)json_encode($payload));
    }

    private function getStorage(): DummyRepositoryStorage
    {
        return ObjectManager::getInstance()->get(DummyRepositoryStorage::class);
    }
}
