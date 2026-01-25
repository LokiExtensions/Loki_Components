<?php declare(strict_types=1);

namespace Loki\Components\Location;

class Address
{
    public function __construct(
        private readonly AddressRenderer $addressRenderer,
        private readonly string $street,
        private readonly string $houseNumber,
        private readonly string $postcode,
        private readonly string $city,
        private readonly string $countryId,
        private readonly ?float $latitude = null,
        private readonly ?float $longitude = null,
        private readonly string $comment = '',
    ) {
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryId(): string
    {
        return $this->countryId;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getInnerHtml(): string
    {
        return $this->addressRenderer->getHtml($this);
    }
}
