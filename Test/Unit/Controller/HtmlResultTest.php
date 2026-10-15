<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Controller;

use Loki\Components\Controller\HtmlResult;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Data\Form\FormKey;
use PHPUnit\Framework\TestCase;

class HtmlResultTest extends TestCase
{
    public function testRenderSetsHeadersAndBody(): void
    {
        $formKey = $this->createMock(FormKey::class);
        $formKey->method('getFormKey')->willReturn('abc123');

        $headers = [];
        $response = $this->createMock(HttpResponse::class);
        $response->method('setHeader')->willReturnCallback(function ($name, $value) use (&$headers, $response) {
            $headers[$name] = $value;
            return $response;
        });
        $response->expects($this->once())->method('setBody')->with('<html></html>');

        $htmlResult = new HtmlResult($formKey);
        $htmlResult->setContents('<html></html>');
        $htmlResult->renderResult($response);

        $this->assertSame('abc123', $headers['X-Magento-Form-Key'] ?? null);
        $this->assertSame('text/html', $headers['Content-Type'] ?? null);
    }
}
