<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\RequiredValidator;

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
