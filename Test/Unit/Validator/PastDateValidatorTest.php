<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\DateValidator;
use Yireo\LokiComponents\Validator\NumberValidator;
use Yireo\LokiComponents\Validator\PastDateValidator;

class PastDateValidatorTest extends TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testWithNoSimpleValues(string $value, true|array $expectedResult): void
    {
        $validator = new PastDateValidator();
        $result = $validator->validate($value);
        $this->assertSame($expectedResult, $result, 'Tested value: '.$value);
    }

    public function getTestData(): array
    {
        return [
            ['', true],
            ['2024-01-01', true],
            [((int)date('Y') + 1) . '-01-01', ['Date needs to be in the past']],
        ];
    }
}
