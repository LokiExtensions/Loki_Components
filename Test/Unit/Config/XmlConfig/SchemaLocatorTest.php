<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Config\XmlConfig;

use Loki\Components\Config\XmlConfig\SchemaLocator;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use PHPUnit\Framework\TestCase;

class SchemaLocatorTest extends TestCase
{
    public function testGetSchema(): void
    {
        $schemaLocator = new SchemaLocator($this->getReader());

        $this->assertSame('/app/code/Loki/Components/etc/loki_components.xsd', $schemaLocator->getSchema());
        $this->assertSame('/app/code/Loki/Components/etc/loki_components.xsd', $schemaLocator->getSchema());
    }

    public function testGetPerFileSchema(): void
    {
        $schemaLocator = new SchemaLocator($this->getReader());

        $this->assertSame('/app/code/Loki/Components/etc/loki_components.xsd', $schemaLocator->getPerFileSchema());
        $this->assertSame('/app/code/Loki/Components/etc/loki_components.xsd', $schemaLocator->getPerFileSchema());
    }

    private function getReader(): Reader
    {
        $reader = $this->createMock(Reader::class);
        $reader->expects($this->once())
            ->method('getModuleDir')
            ->with(Dir::MODULE_ETC_DIR, 'Loki_Components')
            ->willReturn('/app/code/Loki/Components/etc');

        return $reader;
    }
}
