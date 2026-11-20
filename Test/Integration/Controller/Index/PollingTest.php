<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Controller\Index;

use Loki\Components\Controller\Index\Polling;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Data\Form\FormKey;
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
        $this->assertStringContainsString('no-store', $response->getHeader('Cache-Control')->getFieldValue());
    }

    public function testRequiresAValidFormKey(): void
    {
        $controller = ObjectManager::getInstance()->create(Polling::class);
        $this->assertInstanceOf(HttpPostActionInterface::class, $controller);

        $request = ObjectManager::getInstance()->create(HttpRequest::class);
        $request->getHeaders()->addHeaderLine('X-Magento-Form-Key', 'invalid-form-key');
        $this->assertFalse($controller->validateForCsrf($request));

        $request = ObjectManager::getInstance()->create(HttpRequest::class);
        $formKey = ObjectManager::getInstance()->get(FormKey::class)->getFormKey();
        $request->getHeaders()->addHeaderLine('X-Magento-Form-Key', $formKey);
        $this->assertTrue($controller->validateForCsrf($request));

        $response = ObjectManager::getInstance()->create(HttpResponse::class);
        $controller->createCsrfValidationException($request)->getReplaceResult()->renderResult($response);
        $this->assertSame(400, $response->getHttpResponseCode());
    }
}
