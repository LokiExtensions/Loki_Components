<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Location;

use Loki\Components\Location\Address;
use Loki\Components\Location\AddressFactory;
use Loki\Components\Location\AddressRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AddressRendererTest extends TestCase
{
    public function testGetLineSkipsEmptyParts(): void
    {
        $line = $this->getAddressRenderer()->getLine($this->getAddress());

        $this->assertStringContainsString('Main Street', $line);
        $this->assertStringContainsString('Amsterdam', $line);
        $this->assertStringContainsString('1234 AB', $line);
        $this->assertStringNotContainsString(', ,', $line);
        $this->assertStringNotContainsString(',,', $line);
        $this->assertStringStartsNotWith(',', $line);
        $this->assertStringEndsNotWith(',', $line);
    }

    public function testGetHtmlHasNoEmptyLines(): void
    {
        $html = $this->getAddressRenderer()->getHtml($this->getAddress());

        $this->assertStringContainsString('Main Street', $html);
        $this->assertStringContainsString('Amsterdam', $html);
        $this->assertDoesNotMatchRegularExpression('/^<br\s+\/>$/m', $html);
    }

    public function testAddressUsesRenderer(): void
    {
        $address = $this->getAddress();
        $attributes = $address->getAttributes();

        $this->assertSame($this->getAddressRenderer()->getLine($address), $address->getAddressLine());
        $this->assertSame($this->getAddressRenderer()->getHtml($address), $address->getAddressHtml());
        $this->assertSame($address->getAddressLine(), $attributes['addressLine']);
        $this->assertSame('Amsterdam', $attributes['city']);
        $this->assertSame($attributes, $address->jsonSerialize());
    }

    private function getAddress(): Address
    {
        return ObjectManager::getInstance()->get(AddressFactory::class)->create(
            street: 'Main Street',
            houseNumber: '12',
            postcode: '1234 AB',
            city: 'Amsterdam',
            countryId: 'NL',
            company: 'Loki Store',
        );
    }

    private function getAddressRenderer(): AddressRenderer
    {
        return ObjectManager::getInstance()->get(AddressRenderer::class);
    }
}
