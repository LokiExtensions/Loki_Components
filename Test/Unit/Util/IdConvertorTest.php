<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Unit\Util;

use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Util\IdConvertor;

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
