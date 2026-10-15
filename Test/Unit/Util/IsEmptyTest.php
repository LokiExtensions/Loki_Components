<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\IsEmpty;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsEmptyTest extends TestCase
{
    #[DataProvider('getValues')]
    public function testExecute(array $filters, mixed $value, bool $expectedResult): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getFilters')->willReturn($filters);

        $this->assertSame($expectedResult, (new IsEmpty())->execute($component, $value));
    }

    public static function getValues(): array
    {
        return [
            'null' => [[], null, true],
            'empty string' => [[], '', true],
            'empty array' => [[], [], true],
            'false' => [[], false, true],
            'integer zero without number filter' => [[], 0, true],
            'string zero' => [[], '0', false],
            'string' => [[], 'foo', false],
            'array' => [[], ['foo'], false],
            'integer' => [[], 42, false],
            'integer zero with number filter' => [['number'], 0, false],
            'integer zero with positive_number filter' => [['positive_number'], 0, false],
            'null with number filter' => [['number'], null, true],
        ];
    }
}
