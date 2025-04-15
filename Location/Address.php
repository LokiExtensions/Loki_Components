<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Location;

class Address
{
    public function __construct(
        private string $street,
        private string $houseNumber,
        private string $postcode,
        private string $city,
        private string $countryId,
        private ?float $latitude = null,
        private ?float $longitude = null,
        private string $comment = '',
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
}
