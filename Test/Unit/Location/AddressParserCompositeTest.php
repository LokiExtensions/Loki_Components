<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Location;

use Loki\Components\Location\Address;
use Loki\Components\Location\AddressParser\AddressParserInterface;
use Loki\Components\Location\AddressParserComposite;
use PHPUnit\Framework\TestCase;
use stdClass;

class AddressParserCompositeTest extends TestCase
{
    public function testReturnsAddressWithoutParsers(): void
    {
        $address = $this->createMock(Address::class);
        $composite = new AddressParserComposite();

        $this->assertTrue($composite->isEnabled());
        $this->assertSame($address, $composite->parse($address));
    }

    public function testChainsParsersAndSkipsInvalidOnes(): void
    {
        $original = $this->createMock(Address::class);
        $intermediate = $this->createMock(Address::class);
        $final = $this->createMock(Address::class);

        $first = $this->createMock(AddressParserInterface::class);
        $first->expects($this->once())->method('parse')->with($original)->willReturn($intermediate);

        $second = $this->createMock(AddressParserInterface::class);
        $second->expects($this->once())->method('parse')->with($intermediate)->willReturn($final);

        $composite = new AddressParserComposite([$first, new stdClass(), $second]);
        $this->assertSame($final, $composite->parse($original));
    }
}
