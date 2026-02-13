<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\AddressInterface;

class AddressFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly AddressParserComposite $addressParserComposite
    ) {
    }

    public function create(
        string $street = '',
        string $houseNumber = '',
        string $houseNumberAddition = '',
        string $postcode = '',
        string $city = '',
        string $region = '',
        string $countryId = '',
        ?float $latitude = null,
        ?float $longitude = null,
        string $telephone = '',
        string $fax = '',
        string $vatId = '',
        string $comment = '',
        string $company = '',
        string $prefix = '',
        string $firstname = '',
        string $middlename = '',
        string $lastname = '',
        string $suffix = '',
        array $data = [],
    ): Address {
        $address = $this->objectManager->create(Address::class, [
            'street' => $street,
            'houseNumber' => $houseNumber,
            'houseNumberAddition' => $houseNumberAddition,
            'postcode' => $postcode,
            'city' => $city,
            'region' => $region,
            'countryId' => $countryId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'telephone' => $telephone,
            'fax' => $fax,
            'vatId' => $vatId,
            'comment' => $comment,
            'company' => $company,
            'prefix' => $prefix,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'suffix' => $suffix,
            'data' => $data,
        ]);

        return $this->addressParserComposite->parse($address);
    }

    public function createFromQuoteAddress(AddressInterface $quoteAddress): Address
    {
        return $this->create(
            street:  (string)$quoteAddress->getStreetLine(1),
            houseNumber:  (string)$quoteAddress->getStreetLine(2),
            houseNumberAddition:  (string)$quoteAddress->getStreetLine(3) ?? '',
            postcode:  (string)$quoteAddress->getPostcode(),
            city: (string)$quoteAddress->getCity(),
            countryId:  (string)$quoteAddress->getCountryId(),
            telephone:  (string)$quoteAddress->getTelephone(),
            fax:  (string)$quoteAddress->getFax(),
            vatId:  (string)$quoteAddress->getVatId(),
            company:  (string)$quoteAddress->getCompany(),
            prefix:  (string)$quoteAddress->getPrefix(),
            firstname:  (string)$quoteAddress->getFirstname(),
            middlename:  (string)$quoteAddress->getMiddlename(),
            lastname:  (string)$quoteAddress->getLastname(),
            suffix:  (string)$quoteAddress->getSuffix(),
        );
    }
}
