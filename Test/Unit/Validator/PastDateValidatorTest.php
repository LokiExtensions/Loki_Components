<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Loki\Components\Validator\PastDateValidator;

class PastDateValidatorTest extends TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testWithNoSimpleValues(string $value, bool|array $expectedResult): void
    {
        $validator = new PastDateValidator();
        $result = $validator->validate($value);
        $this->assertSame($expectedResult, $result, 'Tested value: ' . $value);
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
