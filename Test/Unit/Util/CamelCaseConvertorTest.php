<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use PHPUnit\Framework\TestCase;
use Loki\Components\Util\CamelCaseConvertor;

class CamelCaseConvertorTest extends TestCase
{
    /**
     * @return void
     * @dataProvider getTestData
     */
    public function testToCamelCase(string $original, string $expected)
    {
        $camelCaseConvertor = new CamelCaseConvertor();
        $this->assertEquals($expected, $camelCaseConvertor->toCamelCase($original));
    }

    public function getTestData(): array
    {
        return [
            ['foo.bar', 'FooBar'],
            ['foo-bar', 'FooBar'],
            ['foo_bar', 'FooBar'],
        ];
    }
}
