<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Index;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Controller\Index\Html;
use Loki\Components\Exception\RedirectException;
use Loki\Components\Test\Integration\Dummy\DummyRepository;
use Loki\Components\Test\Integration\Dummy\DummyRepositoryStorage;
use Loki\Components\Test\Integration\Traits\RegisterTestComponents;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[AppArea('frontend')]
class HtmlTest extends TestCase
{
    use RegisterTestComponents;

    private const HANDLE = 'loki_components_index_test';
    private const DUMMY1 = 'loki-components.test.dummy1';
    private const DUMMY2 = 'loki-components.test.dummy2';

    protected function setUp(): void
    {
        $this->getStorage()->reset();
        $this->registerTestComponents([
            $this->createTestComponentDefinition(self::DUMMY1, [
                'repository' => DummyRepository::class,
                'filters' => ['trim', 'uppercase'],
            ]),
            $this->createTestComponentDefinition(self::DUMMY2, [
                'repository' => DummyRepository::class,
                'validators' => ['required'],
            ]),
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterTestComponents();
        $this->getStorage()->reset();
    }

    #[AppIsolation(true)]
    public function testRendersTargetsWrappedInHtmlDocument(): void
    {
        $this->prepareRequest([self::DUMMY1, 'loki-components-test-dummy2'], []);
        $response = $this->executeController();

        $body = (string)$response->getBody();
        $this->assertStringStartsWith('<html>', $body);
        $this->assertStringEndsWith('</html>', $body);
        $this->assertStringContainsString('>'.self::DUMMY1.'</div>', $body);
        $this->assertStringContainsString('>'.self::DUMMY2.'</div>', $body, 'Targets can be element IDs too');

        $formKey = ObjectManager::getInstance()->get(FormKey::class)->getFormKey();
        $this->assertSame($formKey, $response->getHeader('X-Magento-Form-Key')->getFieldValue());
        $this->assertSame('text/html', $response->getHeader('Content-Type')->getFieldValue());
    }

    #[AppIsolation(true)]
    public function testSavesFilteredValuesInOrderOfRepositoryPriority(): void
    {
        $this->getStorage()->setPriority(self::DUMMY1, 10);
        $this->getStorage()->setPriority(self::DUMMY2, -5);

        $this->prepareRequest([], [
            ['blockName' => self::DUMMY1, 'update' => ' first '],
            ['blockName' => self::DUMMY2, 'update' => 'second'],
        ]);
        $this->executeController();

        $this->assertSame([self::DUMMY2, self::DUMMY1], $this->getStorage()->getSaveOrder());
        $this->assertSame('FIRST', $this->getStorage()->getValue(self::DUMMY1));
        $this->assertSame('second', $this->getStorage()->getValue(self::DUMMY2));
    }

    #[AppIsolation(true)]
    public function testStripsHtmlFromValuesBeforeSaving(): void
    {
        $this->prepareRequest([], [
            ['blockName' => self::DUMMY2, 'update' => '<script>alert(1)</script>foo'],
        ]);
        $this->executeController();

        $this->assertSame('alert(1)foo', $this->getStorage()->getValue(self::DUMMY2));
    }

    #[AppIsolation(true)]
    public function testDoesNotSaveInvalidValues(): void
    {
        $this->prepareRequest([], [
            ['blockName' => self::DUMMY2, 'update' => ''],
        ]);
        $this->executeController();

        $this->assertFalse($this->getStorage()->hasValue(self::DUMMY2));
    }

    #[AppIsolation(true)]
    public function testSkipsUpdatesWithoutBlockName(): void
    {
        $this->prepareRequest([], [
            ['update' => 'foo'],
            ['blockName' => ['invalid'], 'update' => 'foo'],
        ]);
        $this->executeController();

        $this->assertSame([], $this->getStorage()->getSaveOrder());
    }

    #[AppIsolation(true)]
    public function testSkipsUpdatesForUnknownBlocks(): void
    {
        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->callback(fn ($message) => str_contains((string)$message, 'loki-components.test.unknown')));

        $this->prepareRequest([self::DUMMY2], [
            ['blockName' => 'loki-components.test.unknown', 'update' => 'foo'],
            ['blockName' => self::DUMMY2, 'update' => 'bar'],
        ]);

        $controller = ObjectManager::getInstance()->create(Html::class, ['messageManager' => $messageManager]);
        $response = $this->renderResult($controller->execute());

        $this->assertSame('bar', $this->getStorage()->getValue(self::DUMMY2));
        $this->assertStringContainsString(self::DUMMY2, (string)$response->getBody());
    }

    #[AppIsolation(true)]
    public function testSkipsUpdatesForBlocksThatAreNoComponent(): void
    {
        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->once())->method('addErrorMessage');

        $this->prepareRequest([], [
            ['blockName' => 'loki-components.utils.local-messages', 'update' => 'foo'],
            ['blockName' => self::DUMMY2, 'update' => 'bar'],
        ]);

        $controller = ObjectManager::getInstance()->create(Html::class, ['messageManager' => $messageManager]);
        $controller->execute();

        $this->assertSame('bar', $this->getStorage()->getValue(self::DUMMY2));
    }

    #[AppIsolation(true)]
    public function testRenderFlagZeroSkipsRendering(): void
    {
        $this->prepareRequest([self::DUMMY1], [
            ['blockName' => self::DUMMY1, 'update' => 'foo', 'render' => 0],
        ]);
        $response = $this->executeController();

        $this->assertSame('FOO', $this->getStorage()->getValue(self::DUMMY1));
        $this->assertStringNotContainsString(self::DUMMY1, (string)$response->getBody());
    }

    #[AppIsolation(true)]
    public function testRenderFlagZeroInUpdateValueSkipsRendering(): void
    {
        $this->prepareRequest([self::DUMMY1], [
            ['blockName' => self::DUMMY1, 'update' => ['mandate_id' => 'foo', 'render' => 0]],
        ]);
        $response = $this->executeController();

        $this->assertTrue($this->getStorage()->hasValue(self::DUMMY1));
        $this->assertStringNotContainsString(self::DUMMY1, (string)$response->getBody());
    }

    #[AppIsolation(true)]
    public function testRedirectExceptionReturnsJsonRedirect(): void
    {
        $redirectException = new RedirectException('Please login first');
        $redirectException->setUrl('https://example.com/login');
        $this->getStorage()->throwOnSave(self::DUMMY1, $redirectException);

        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->once())->method('addErrorMessage')->with('Please login first');

        $this->prepareRequest([self::DUMMY1], [
            ['blockName' => self::DUMMY1, 'update' => 'foo'],
            ['blockName' => self::DUMMY2, 'update' => 'bar'],
        ]);

        $controller = ObjectManager::getInstance()->create(Html::class, ['messageManager' => $messageManager]);
        $result = $controller->execute();
        $this->assertInstanceOf(Json::class, $result);

        $response = $this->renderResult($result);
        $this->assertSame(['redirect' => 'https://example.com/login'], json_decode((string)$response->getBody(), true));
        $this->assertSame('https://example.com/login', $response->getHeader('X-Loki-Redirect')->getFieldValue());
    }

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/debug', 0, 'store', 'default')]
    public function testExceptionIsAddedAsErrorMessage(): void
    {
        $this->getStorage()->throwOnSave(self::DUMMY1, new RuntimeException('Something broke'));

        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->callback(fn ($message) => (string)$message === 'Something broke'));
        $messageManager->expects($this->never())->method('addComplexErrorMessage');

        $this->prepareRequest([self::DUMMY1], [
            ['blockName' => self::DUMMY1, 'update' => 'foo'],
            ['blockName' => self::DUMMY2, 'update' => 'bar'],
        ]);

        $controller = ObjectManager::getInstance()->create(Html::class, ['messageManager' => $messageManager]);
        $response = $this->renderResult($controller->execute());

        $this->assertSame('bar', $this->getStorage()->getValue(self::DUMMY2), 'Other updates continue after an exception');
        $this->assertStringContainsString(self::DUMMY1, (string)$response->getBody());
    }

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/debug', 1, 'store', 'default')]
    public function testExceptionIsAddedWithTraceInDebugAndDeveloperMode(): void
    {
        $this->getStorage()->throwOnSave(self::DUMMY1, new RuntimeException('Something broke'));

        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->never())->method('addErrorMessage');
        $messageManager->expects($this->once())
            ->method('addComplexErrorMessage')
            ->with('lokiComponentException', $this->callback(function (array $data) {
                return $data['message'] === 'Something broke'
                    && is_array($data['trace'])
                    && str_ends_with($data['file'], 'HtmlTest.php');
            }));

        $appState = $this->createMock(AppState::class);
        $appState->method('getMode')->willReturn(AppState::MODE_DEVELOPER);

        $this->prepareRequest([], [['blockName' => self::DUMMY1, 'update' => 'foo']]);

        $controller = ObjectManager::getInstance()->create(Html::class, [
            'messageManager' => $messageManager,
            'appState' => $appState,
        ]);
        $controller->execute();
    }

    #[AppIsolation(true)]
    public function testRejectsTamperedPayload(): void
    {
        $this->prepareRequest([self::DUMMY1], []);

        $request = ObjectManager::getInstance()->get(HttpRequest::class);
        $payload = json_decode((string)$request->getContent(), true);
        $payload['handles'][] = 'customer_account';
        $request->setContent((string)json_encode($payload));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payload was tampered with');
        ObjectManager::getInstance()->create(Html::class)->execute();
    }

    private function prepareRequest(array $targets, array $updates): void
    {
        $payload = [
            'targets' => $targets,
            'handles' => [self::HANDLE],
            'pageHandles' => ['1column'],
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

    private function executeController(): HttpResponse
    {
        $controller = ObjectManager::getInstance()->create(Html::class);
        return $this->renderResult($controller->execute());
    }

    private function renderResult(mixed $result): HttpResponse
    {
        $this->assertInstanceOf(ResultInterface::class, $result);

        $response = ObjectManager::getInstance()->create(HttpResponse::class);
        $result->renderResult($response);

        return $response;
    }

    private function getStorage(): DummyRepositoryStorage
    {
        return ObjectManager::getInstance()->get(DummyRepositoryStorage::class);
    }
}
