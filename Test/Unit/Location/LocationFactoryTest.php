<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Location;

use Loki\Components\Location\Address;
use Loki\Components\Location\Location;
use Loki\Components\Location\LocationFactory;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;

class LocationFactoryTest extends TestCase
{
    public function testCreatePassesNamedArguments(): void
    {
        $address = $this->createMock(Address::class);
        $location = $this->createMock(Location::class);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('create')
            ->with(Location::class, [
                'address' => $address,
                'businessHours' => null,
                'id' => 'store-1',
                'label' => 'Store 1',
                'value' => ['code' => 1],
                'distance' => '1 km',
                'pickupFrom' => 'tomorrow',
                'price' => 395,
                'data' => ['foo' => 'bar'],
            ])
            ->willReturn($location);

        $factory = new LocationFactory($objectManager);
        $this->assertSame(
            $location,
            $factory->create($address, null, 'store-1', 'Store 1', ['code' => 1], '1 km', 'tomorrow', 395, ['foo' => 'bar'])
        );
    }
}
