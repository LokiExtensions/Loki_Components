<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Magento\Framework\DataObject;

class Address extends DataObject
{
    public function __construct(
        private readonly AddressRenderer $addressRenderer,
        private string $street = '',
        private string $houseNumber = '',
        private string $houseNumberAddition = '',
        private string $postcode = '',
        private string $city = '',
        private string $region = '',
        private string $countryId = '',
        private ?float $latitude = null,
        private ?float $longitude = null,
        private string $telephone = '',
        private string $fax = '',
        private string $vatId = '',
        private string $comment = '',
        private string $company = '',
        private string $prefix = '',
        private string $firstname = '',
        private string $middlename = '',
        private string $lastname = '',
        private string $suffix = '',
        array $data = []
    ) {
        parent::__construct($data);
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function setHouseNumberAddition(string $houseNumberAddition): void
    {
        $this->houseNumberAddition = $houseNumberAddition;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function setCountryId(string $countryId): void
    {
        $this->countryId = $countryId;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }

    public function setVatId(string $vatId): void
    {
        $this->vatId = $vatId;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function setMiddlename(string $middlename): void
    {
        $this->middlename = $middlename;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
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

    public function getAddressLine(): string
    {
        return $this->addressRenderer->getLine($this);
    }

    public function getInnerHtml(): string
    {
        return $this->addressRenderer->getHtml($this);
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getHouseNumberAddition(): string
    {
        return $this->houseNumberAddition;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getMiddlename(): string
    {
        return $this->middlename;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getFax(): string
    {
        return $this->fax;
    }

    public function getVatId(): string
    {
        return $this->vatId;
    }
}
