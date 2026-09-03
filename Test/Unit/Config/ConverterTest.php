<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Config;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Loki\Components\Config\XmlConfig\Converter;

class ConverterTest extends TestCase
{
    public function testConvertProducesGroupsAndComponentsKeyedByName(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<components>
    <group name="default"/>
    <component name="loki.one">
        <target name="loki.target.a"/>
        <validator name="required"/>
        <filter name="trim"/>
    </component>
    <component name="loki.two" group="default"/>
</components>
XML;

        $result = (new Converter())->convert($this->loadDom($xml));

        $this->assertArrayHasKey('groups', $result);
        $this->assertArrayHasKey('components', $result);

        $this->assertArrayHasKey('default', $result['groups']);
        $this->assertArrayHasKey('loki.one', $result['components']);
        $this->assertArrayHasKey('loki.two', $result['components']);

        $componentOne = $result['components']['loki.one'];
        $this->assertSame('default', $componentOne['group']);
        $this->assertArrayHasKey('loki.target.a', $componentOne['targets']);
        $this->assertArrayHasKey('required', $componentOne['validators']);
        $this->assertArrayHasKey('trim', $componentOne['filters']);
        $this->assertFalse($componentOne['validators']['required']['disabled']);
    }

    public function testConvertCapturesDisabledFlag(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<components>
    <group name="default"/>
    <component name="loki.one">
        <validator name="required" disabled="true"/>
    </component>
</components>
XML;

        $result = (new Converter())->convert($this->loadDom($xml));

        $this->assertTrue($result['components']['loki.one']['validators']['required']['disabled']);
    }

    private function loadDom(string $xml): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        return $dom;
    }
}
