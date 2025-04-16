<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Location;

use Yireo\LokiComponents\Location\Address\BusinessHours;

class Location
{
    public function __construct(
        private string $id,
        private string $code,
        private string $label,
        private int $distance,
        private Address $address,
        private ?BusinessHours $businessHours = null,
        private ?string $pickupFrom = null,
        private ?int $price = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getBusinessHours(): ?BusinessHours
    {
        return $this->businessHours;
    }

    public function getPickupFrom(): ?string
    {
        return $this->pickupFrom;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }
}
