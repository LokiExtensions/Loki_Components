<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Location;

use Loki\Components\Location\Address;
use Loki\Components\Location\Address\BusinessHours;
use Loki\Components\Location\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testIdAndLabelFallBackToCompany(): void
    {
        $location = new Location($this->getAddress('Loki Store'));

        $this->assertSame('Loki Store', $location->getLabel());
        $this->assertSame('Loki Store', $location->getId());
    }

    public function testIdFallsBackToLabel(): void
    {
        $location = new Location($this->getAddress('Loki Store'), null, '', 'Custom label');
        $this->assertSame('Custom label', $location->getId());
    }

    public function testGetters(): void
    {
        $address = $this->getAddress('Loki Store');
        $businessHours = $this->createMock(BusinessHours::class);
        $location = new Location($address, $businessHours, 'store-1', 'Store 1', ['code' => 1], '1.2 km', 'tomorrow', 395);

        $this->assertSame($address, $location->getAddress());
        $this->assertSame($businessHours, $location->getBusinessHours());
        $this->assertSame('store-1', $location->getId());
        $this->assertSame('Store 1', $location->getLabel());
        $this->assertSame(['code' => 1], $location->getValue());
        $this->assertSame('1.2 km', $location->getDistance());
        $this->assertSame('tomorrow', $location->getPickupFrom());
        $this->assertSame(395, $location->getPrice());
    }

    public function testGetAttributesMergesAddressAttributes(): void
    {
        $location = new Location(
            $this->getAddress('Loki Store', ['city' => 'Amsterdam', 'id' => 'overruled']),
            null,
            'store-1',
            '',
            [],
            '1 km',
            null,
            null,
            ['extra' => 'data']
        );

        $attributes = $location->getAttributes();
        $this->assertSame('Amsterdam', $attributes['city']);
        $this->assertSame('store-1', $attributes['id']);
        $this->assertSame('Loki Store', $attributes['label']);
        $this->assertSame('1 km', $attributes['distance']);
        $this->assertNull($attributes['pickupFrom']);
        $this->assertNull($attributes['businessHours']);
        $this->assertSame(['extra' => 'data'], $attributes['data']);
        $this->assertSame($attributes, $location->jsonSerialize());
    }

    public function testGetAttributesContainsPrice(): void
    {
        $location = new Location($this->getAddress('Loki Store'), null, 'store-1', '', [], '', null, 395);

        $attributes = $location->getAttributes();
        $this->assertArrayHasKey('price', $attributes);
        $this->assertSame(395, $attributes['price']);
    }

    public function testAddressIsJsonSerializable(): void
    {
        $address = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttributes'])
            ->getMock();
        $address->method('getAttributes')->willReturn(['city' => 'Amsterdam']);

        $this->assertSame('{"city":"Amsterdam"}', json_encode($address));
    }

    private function getAddress(string $company, array $attributes = []): Address
    {
        $address = $this->createMock(Address::class);
        $address->method('getCompany')->willReturn($company);
        $address->method('getAttributes')->willReturn($attributes);

        return $address;
    }
}
