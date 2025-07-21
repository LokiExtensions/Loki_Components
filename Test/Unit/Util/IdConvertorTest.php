<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use PHPUnit\Framework\TestCase;
use Loki\Components\Util\IdConvertor;

class IdConvertorTest extends TestCase
{
    /**
     * @return void
     * @dataProvider getTestData
     */
    public function testToCamelCase(string $original, string $expected)
    {
        $convertor = new IdConvertor();
        $this->assertEquals($expected, $convertor->toElementId($original));
    }

    public function getTestData(): array
    {
        return [
            ['foo.bar', 'foo-bar'],
            ['foo-bar', 'foo-bar'],
            ['foo_bar', 'foo-bar'],
        ];
    }
}
