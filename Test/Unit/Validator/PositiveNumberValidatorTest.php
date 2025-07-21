<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\PositiveNumberValidator;

class PositiveNumberValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $validator = new PositiveNumberValidator();
        $this->assertTrue($validator->validate(1));
        $this->assertTrue($validator->validate(0));
        $this->assertTrue($validator->validate(-0));
        $this->assertIsArray($validator->validate(-1));
        $this->assertTrue($validator->validate('0'));
        $this->assertIsArray($validator->validate('a0'));
        $this->assertIsArray($validator->validate(null));
    }
}
