<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Test\Unit\Stub\MagentoProductMetaDataStub;
use Loki\Components\Test\Unit\Stub\MageOSProductMetaDataStub;
use Loki\Components\Util\MagentoVersion;
use Magento\Framework\App\ProductMetadataInterface;
use PHPUnit\Framework\TestCase;

class MagentoVersionTest extends TestCase
{
    /**
     * @param string $version
     * @param string $operator
     * @param bool   $expected
     *
     * @return void
     * @dataProvider providerTestIsMagentoVersion
     */
    public function testIsMagentoVersion(string $version, string $operator, bool $expected): void
    {
        $productMetaData = new MagentoProductMetaDataStub();
        $magentoVersion = new MagentoVersion($productMetaData);
        $this->assertFalse($magentoVersion->isMageOS());
        $this->assertSame($expected, $magentoVersion->isMagentoVersion($version, $operator));
    }

    static public function providerTestIsMagentoVersion(): array
    {
        return [
            ['2.4.6', 'eq', false],
            ['2.4.6', 'gt', false],
            ['2.4.7-p3', 'gt', false],
            ['2.4.7-p3', 'lt', true],
            ['2.4.7-p4', 'eq', true],
            ['2.4.7-p5', 'gt', true],
            ['2.4.7-p5', 'lt', false],
            ['2.4.8', 'eq', false],
            ['2.4.8', 'gt', true],
            ['2.4.8', 'lt', false],
        ];
    }

    /**
     * @param string $version
     * @param string $operator
     * @param bool   $expected
     *
     * @return void
     * @dataProvider providerTestIsMageOSVersion
     */
    public function testIsMageOSVersion(string $version, string $operator, bool $expected): void
    {
        $productMetaData = new MageOSProductMetaDataStub();
        $magentoVersion = new MagentoVersion($productMetaData);
        $this->assertTrue($magentoVersion->isMageOS());
        $this->assertSame($expected, $magentoVersion->isMageOSVersion($version, $operator));
    }

    static public function providerTestIsMageOSVersion(): array
    {
        return [
            ['1.2.1-p2', 'lt', true],
            ['1.2.1-p2', 'eq', false],
            ['1.2.1-p2', 'gt', false],
            ['1.3.0', 'eq', true],
            ['1.3.0', 'lt', false],
            ['1.3.0', 'gt', false],
            ['1.3.1', 'lt', false],
            ['1.3.1', 'eq', false],
            ['1.3.1', 'gt', true],
        ];
    }
}
