<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\NumberValidator;

class NumberValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $validator = new NumberValidator();
        $this->assertTrue($validator->validate(0));
        $this->assertTrue($validator->validate('0'));
        $this->assertIsArray($validator->validate('a0'));
        $this->assertIsArray($validator->validate(null));
    }
}
