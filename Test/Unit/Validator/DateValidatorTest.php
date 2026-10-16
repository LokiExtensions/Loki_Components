<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\DateValidator;

class DateValidatorTest extends TestCase
{
    public function testWithNoSimpleValues(): void
    {
        $validator = new DateValidator();
        $this->assertTrue($validator->validate('2024-01-01'));
        $this->assertIsArray($validator->validate('test'));
    }

    public function testWithImpossibleDates(): void
    {
        $validator = new DateValidator();
        $this->assertTrue($validator->validate('2024-02-29'));
        $this->assertIsArray($validator->validate('2023-02-29'));
        $this->assertIsArray($validator->validate('2024-13-01'));
        $this->assertIsArray($validator->validate('2024-01-32'));
    }
}
