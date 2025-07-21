<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Loki\Components\Location\Address\BusinessHours;

class Location
{
    public function __construct(
        private readonly string $id,
        private readonly string $code,
        private readonly string $label,
        private readonly int $distance,
        private readonly Address $address,
        private readonly ?BusinessHours $businessHours = null,
        private readonly ?string $pickupFrom = null,
        private readonly ?int $price = null,
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
