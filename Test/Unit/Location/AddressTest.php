<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Location;

use Loki\Components\Location\Address;
use Loki\Components\Location\AddressRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testDefaults(): void
    {
        $address = new Address($this->createMock(AddressRenderer::class));

        $this->assertSame('', $address->getStreet());
        $this->assertSame('', $address->getCountryId());
        $this->assertNull($address->getLatitude());
        $this->assertNull($address->getLongitude());
    }

    #[DataProvider('getStringProperties')]
    public function testStringSettersAndGetters(string $property): void
    {
        $address = new Address($this->createMock(AddressRenderer::class));
        $address->{'set' . $property}('foobar');

        $this->assertSame('foobar', $address->{'get' . $property}());
    }

    public static function getStringProperties(): array
    {
        return [
            ['Street'],
            ['HouseNumber'],
            ['HouseNumberAddition'],
            ['Postcode'],
            ['City'],
            ['Region'],
            ['CountryId'],
            ['Telephone'],
            ['Fax'],
            ['VatId'],
            ['Comment'],
            ['Company'],
            ['Prefix'],
            ['Firstname'],
            ['Middlename'],
            ['Lastname'],
            ['Suffix'],
        ];
    }

    public function testCoordinates(): void
    {
        $address = new Address($this->createMock(AddressRenderer::class));
        $address->setLatitude(52.37);
        $address->setLongitude(4.89);

        $this->assertSame(52.37, $address->getLatitude());
        $this->assertSame(4.89, $address->getLongitude());

        $address->setLatitude(null);
        $this->assertNull($address->getLatitude());
    }

    public function testRenderingIsDelegatedToAddressRenderer(): void
    {
        $addressRenderer = $this->createMock(AddressRenderer::class);
        $address = new Address($addressRenderer, 'Main street');

        $addressRenderer->method('getLine')->with($address)->willReturn('Main street, Amsterdam');
        $addressRenderer->method('getHtml')->with($address)->willReturn('<p>Main street</p>');

        $this->assertSame('Main street, Amsterdam', $address->getAddressLine());
        $this->assertSame('<p>Main street</p>', $address->getAddressHtml());
        $this->assertSame('<p>Main street</p>', $address->getInnerHtml());
    }

    public function testGetAttributes(): void
    {
        $addressRenderer = $this->createMock(AddressRenderer::class);
        $addressRenderer->method('getLine')->willReturn('line');
        $addressRenderer->method('getHtml')->willReturn('html');

        $address = new Address(
            $addressRenderer,
            'Main street',
            '1',
            'A',
            '1234 AB',
            'Amsterdam',
            'NH',
            'NL',
            52.37,
            4.89,
            '0123456789',
            '0987654321',
            'NL123',
            'Comment',
            'Yireo',
            'Mr',
            'John',
            'J',
            'Doe',
            'Jr'
        );

        $expected = [
            'street' => 'Main street',
            'houseNumber' => '1',
            'houseNumberAddition' => 'A',
            'postcode' => '1234 AB',
            'city' => 'Amsterdam',
            'region' => 'NH',
            'countryId' => 'NL',
            'latitude' => 52.37,
            'longitude' => 4.89,
            'vatId' => 'NL123',
            'comment' => 'Comment',
            'company' => 'Yireo',
            'prefix' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'J',
            'lastname' => 'Doe',
            'suffix' => 'Jr',
            'fax' => '0987654321',
            'telephone' => '0123456789',
            'addressLine' => 'line',
            'addressHtml' => 'html',
        ];

        $this->assertSame($expected, $address->getAttributes());
        $this->assertSame($expected, $address->jsonSerialize());
        $this->assertSame(json_encode($expected), json_encode($address));
    }

    public function testDataObjectDataIsKept(): void
    {
        $address = new Address($this->createMock(AddressRenderer::class), data: ['foo' => 'bar']);
        $this->assertSame('bar', $address->getData('foo'));
    }
}
