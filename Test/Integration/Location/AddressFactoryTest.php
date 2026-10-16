<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Location;

use Loki\Components\Location\Address;
use Loki\Components\Location\AddressFactory;
use Loki\Components\Location\AddressParserComposite;
use Loki\Components\Location\AddressParser\AddressParserInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AddressFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $address = $this->getAddressFactory()->create(
            street: 'Main Street',
            houseNumber: '12',
            postcode: '1234 AB',
            city: 'Amsterdam',
            countryId: 'NL',
            latitude: 52.37,
            longitude: 4.89,
            company: 'Loki Store',
        );

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Main Street', $address->getStreet());
        $this->assertSame('12', $address->getHouseNumber());
        $this->assertSame('1234 AB', $address->getPostcode());
        $this->assertSame('Amsterdam', $address->getCity());
        $this->assertSame('NL', $address->getCountryId());
        $this->assertSame(52.37, $address->getLatitude());
        $this->assertSame(4.89, $address->getLongitude());
        $this->assertSame('Loki Store', $address->getCompany());
    }

    public function testCreateFromQuoteAddressMapsStreetLines(): void
    {
        /** @var QuoteAddress $quoteAddress */
        $quoteAddress = ObjectManager::getInstance()->create(QuoteAddress::class);
        $quoteAddress->setStreet(['Main Street', '12', 'A']);
        $quoteAddress->setPostcode('1234 AB');
        $quoteAddress->setCity('Amsterdam');
        $quoteAddress->setCountryId('NL');
        $quoteAddress->setTelephone('0612345678');
        $quoteAddress->setCompany('Loki Store');
        $quoteAddress->setFirstname('Paul');
        $quoteAddress->setLastname('Laywright');

        $address = $this->getAddressFactory()->createFromQuoteAddress($quoteAddress);

        $this->assertSame('Main Street', $address->getStreet());
        $this->assertSame('12', $address->getHouseNumber());
        $this->assertSame('A', $address->getHouseNumberAddition());
        $this->assertSame('1234 AB', $address->getPostcode());
        $this->assertSame('Amsterdam', $address->getCity());
        $this->assertSame('NL', $address->getCountryId());
        $this->assertSame('0612345678', $address->getTelephone());
        $this->assertSame('Loki Store', $address->getCompany());
        $this->assertSame('Paul', $address->getFirstname());
        $this->assertSame('Laywright', $address->getLastname());
    }

    public function testCreateFromQuoteAddressMapsRegion(): void
    {
        /** @var QuoteAddress $quoteAddress */
        $quoteAddress = ObjectManager::getInstance()->create(QuoteAddress::class);
        $quoteAddress->setStreet(['Main Street', '12']);
        $quoteAddress->setCountryId('NL');
        $quoteAddress->setRegion('Noord-Holland');

        $address = $this->getAddressFactory()->createFromQuoteAddress($quoteAddress);

        $this->assertSame('Noord-Holland', $address->getRegion());
    }

    public function testCreateRunsAddressParsers(): void
    {
        $parser = new class implements AddressParserInterface {
            public function isEnabled(): bool
            {
                return true;
            }

            public function parse(Address $address): Address
            {
                $address->setCity(strtoupper($address->getCity()));
                return $address;
            }
        };

        $addressFactory = ObjectManager::getInstance()->create(AddressFactory::class, [
            'addressParserComposite' => new AddressParserComposite([$parser]),
        ]);

        $this->assertSame('AMSTERDAM', $addressFactory->create(city: 'Amsterdam')->getCity());
    }

    private function getAddressFactory(): AddressFactory
    {
        return ObjectManager::getInstance()->get(AddressFactory::class);
    }
}
