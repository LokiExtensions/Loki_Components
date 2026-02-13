<?php declare(strict_types=1);

namespace Loki\Components\Location\AddressParser;

use Loki\Components\Location\Address;

interface AddressParserInterface
{
    public function isEnabled(): bool;
    public function parse(Address $address): Address;
}
