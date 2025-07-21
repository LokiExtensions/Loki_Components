<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\RequiredValidator;

class RequiredValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $validator = new RequiredValidator();
        $this->assertTrue($validator->validate(1));
        $this->assertIsArray($validator->validate(0));
        $this->assertIsArray($validator->validate(''));
        $this->assertIsArray($validator->validate(null));
    }
}
