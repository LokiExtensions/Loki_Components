<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\Ajax;
use Loki\Components\Util\IsEmpty;
use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\RequiredValidator;

class RequiredValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $ajax = $this->createMock(Ajax::class);
        $ajax->method('isEmpty')->willReturn(true);

        $validator = new RequiredValidator(new IsEmpty(), $ajax);
        $this->assertTrue($validator->validate(1, $component));
        $this->assertIsArray($validator->validate(0, $component));
        $this->assertIsArray($validator->validate('', $component));
        $this->assertIsArray($validator->validate(null, $component));
    }
}
