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

    public function testConvertTreatsFalseAsNotDisabled(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<components>
    <group name="default">
        <target name="loki.group.target" disabled="false"/>
    </group>
    <component name="loki.one">
        <target name="loki.target.a" disabled="false"/>
        <target name="loki.target.b" disabled="0"/>
        <target name="loki.target.c" disabled="1"/>
        <validator name="required" disabled="false"/>
        <filter name="trim" disabled="false"/>
    </component>
</components>
XML;

        $result = (new Converter())->convert($this->loadDom($xml));
        $componentOne = $result['components']['loki.one'];

        $this->assertFalse($result['groups']['default']['targets']['loki.group.target']['disabled']);
        $this->assertFalse($componentOne['targets']['loki.target.a']['disabled']);
        $this->assertFalse($componentOne['targets']['loki.target.b']['disabled']);
        $this->assertTrue($componentOne['targets']['loki.target.c']['disabled']);
        $this->assertFalse($componentOne['validators']['required']['disabled']);
        $this->assertFalse($componentOne['filters']['trim']['disabled']);
    }

    private function loadDom(string $xml): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        return $dom;
    }
}
