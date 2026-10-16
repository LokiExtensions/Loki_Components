<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Controller;

use Loki\Components\Util\Controller\RenderFlag;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RenderFlagTest extends TestCase
{
    #[DataProvider('getUpdates')]
    public function testAllowRendering(array $updates, bool $expected): void
    {
        $this->assertSame($expected, (new RenderFlag())->allowRendering($updates));
    }

    public static function getUpdates(): array
    {
        return [
            'no updates' => [[], true],
            'no render flag' => [[['blockName' => 'foo', 'update' => 'bar']], true],
            'render flag 1' => [[['render' => 1]], true],
            'render flag true' => [[['render' => true]], true],
            'render flag 0' => [[['render' => 0]], false],
            'render flag false' => [[['render' => false]], false],
            'render flag null' => [[['render' => null]], true],
            'render flag 1 in value' => [[['update' => ['foo' => 'bar', 'render' => 1]]], true],
            'render flag 0 in value' => [[['update' => ['foo' => 'bar', 'render' => 0]]], false],
            'scalar value' => [[['update' => 'render']], true],
            'one of multiple updates disables rendering' => [[['update' => 'foo'], ['render' => 0]], false],
        ];
    }
}
