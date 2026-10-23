<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Config\XmlConfig;

use Magento\Framework\Config\ReaderInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class VirtualFileReaderTest extends TestCase
{
    public function testReadsMergedLokiComponentsXml(): void
    {
        $data = $this->getReader()->read('global');

        $this->assertArrayHasKey('groups', $data);
        $this->assertArrayHasKey('components', $data);
        $this->assertArrayHasKey('default', $data['groups']);
        $this->assertSame('default', $data['groups']['default']['name']);
    }

    public function testReadsComponentsOfThisModule(): void
    {
        $components = $this->getReader()->read('global')['components'];

        foreach ([
            'loki-components.modal',
            'loki-components.test.dummy1',
            'loki-components.test.dummy2',
            'loki-components.test.dummy-html',
        ] as $componentName) {
            $this->assertArrayHasKey($componentName, $components);
            $component = $components[$componentName];
            $this->assertSame($componentName, $component['name']);
            $this->assertSame('default', $component['group']);
            $this->assertIsArray($component['targets']);
            $this->assertIsArray($component['validators']);
            $this->assertIsArray($component['filters']);
        }
    }

    private function getReader(): ReaderInterface
    {
        $reader = Bootstrap::getObjectManager()->create('Loki\Components\Config\XmlConfig\VirtualFileReader');
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        return $reader;
    }
}
