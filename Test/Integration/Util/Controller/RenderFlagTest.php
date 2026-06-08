<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Controller\Index\Html;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class RenderFlagTest extends TestCase
{
    private const TARGET_BLOCK = 'loki-components.utils.local-messages';
    private const TARGET_MARKER = 'loki-components-messages';
    private const HANDLE = 'loki_messages';

    #[DataProvider('renderFlagDataProvider')]
    #[AppIsolation(true)]
    public function testRenderFlagControlsRendering(array $update, bool $shouldRender): void
    {
        $this->prepareRequest([
            'targets' => [self::TARGET_BLOCK],
            'handles' => [self::HANDLE],
            'updates' => [$update],
            'request' => [],
        ]);

        $body = $this->executeController();

        if ($shouldRender) {
            $this->assertStringContainsString(
                self::TARGET_MARKER,
                $body,
                'Expected the target component to be rendered'
            );

            return;
        }

        $this->assertStringNotContainsString(
            self::TARGET_MARKER,
            $body,
            'Expected the target component not to be rendered'
        );
    }

    public static function renderFlagDataProvider(): array
    {
        return [
            'render flag set to 1 renders' => [
                ['render' => 1],
                true,
            ],
            'render flag omitted renders' => [
                [],
                true,
            ],
            'render flag set to 0 does not render' => [
                ['render' => 0],
                false,
            ],
            'render flag set to false does not render' => [
                ['render' => false],
                false,
            ],
        ];
    }

    private function prepareRequest(array $payload): void
    {
        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));

        $request = ObjectManager::getInstance()->get(HttpRequest::class);
        $request->setHeaders($headers);
        $request->setContent(json_encode($payload));
    }

    private function executeController(): string
    {
        $controller = ObjectManager::getInstance()->create(Html::class);

        $result = $controller->execute();
        $this->assertInstanceOf(ResultInterface::class, $result);

        $response = ObjectManager::getInstance()->create(HttpResponse::class);
        $result->renderResult($response);

        return (string)$response->getBody();
    }
}
