<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Loki\Components\Location\AddressParser\AddressParserInterface;

class AddressParserComposite implements AddressParserInterface
{
    public function __construct(
        private $addressParsers = []
    ) {
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function parse(Address $address): Address
    {
        if (empty($this->addressParsers)) {
            return $address;
        }

        foreach ($this->addressParsers as $addressParser) {
            if (false === $addressParser instanceof AddressParserInterface) {
                continue;
            }

            $address = $addressParser->parse($address);
        }

        return $address;
    }
}
