<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\DateValidator;
use Yireo\LokiComponents\Validator\NumberValidator;

class DateValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $validator = new DateValidator();
        $this->assertTrue($validator->validate('2024-01-01'));
        $this->assertIsArray($validator->validate('test'));
    }
}
