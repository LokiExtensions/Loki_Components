<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\Ajax;
use Loki\Components\Util\IsEmpty;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\AbstractBlock;
use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\RequiredValidator;

class RequiredValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $block = $this->createMock(AbstractBlock::class);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getBlock')->willReturn($block);

        $ajax = $this->createMock(Ajax::class);
        $ajax->method('isAjax')->willReturn(true);

        $request = $this->createMock(RequestInterface::class);
        $validator = new RequiredValidator(new IsEmpty(), $request, $ajax);
        $this->assertTrue($validator->validate(1, $component));
        $this->assertIsArray($validator->validate(0, $component));
        $this->assertIsArray($validator->validate('', $component));
        $this->assertIsArray($validator->validate(null, $component));
    }
}
