<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Controller;

use Loki\Components\Util\Controller\FormKeyHeader;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Form\FormKey\Validator;
use PHPUnit\Framework\TestCase;

class FormKeyHeaderTest extends TestCase
{
    public function testUsesFormKeyFromHeader(): void
    {
        $request = $this->createMock(HttpRequest::class);
        $request->method('getHeader')->with('X-Magento-Form-Key')->willReturn('header-key');
        $request->expects($this->once())->method('setParam')->with('form_key', 'header-key');

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')->with($request)->willReturn(true);

        $this->assertTrue((new FormKeyHeader($validator))->validate($request));
    }

    public function testFallsBackToFormKeyParameterWithoutHeader(): void
    {
        $request = $this->createMock(HttpRequest::class);
        $request->method('getHeader')->willReturn(false);
        $request->expects($this->never())->method('setParam');

        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturn(false);

        $this->assertFalse((new FormKeyHeader($validator))->validate($request));
    }
}
