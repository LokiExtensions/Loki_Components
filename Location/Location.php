<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Loki\Components\Location\Address\BusinessHours;
use Magento\Framework\DataObject;

class Location extends DataObject
{
    public function __construct(
        private readonly Address $address,
        private readonly ?BusinessHours $businessHours = null,
        private readonly string $id = '',
        private readonly string $code = '',
        private readonly string $label = '',
        private readonly int $distance = 0,
        private readonly ?string $pickupFrom = null,
        private readonly ?int $price = null,
        array $data = []
    ) {
        parent::__construct($data);
    }

    public function getId(): string
    {
        return !empty($this->id) ? $this->id : $this->getLabel();
    }

    public function getCode(): string
    {
        return !empty($this->code) ? $this->code : $this->getId();
    }

    public function getLabel(): string
    {
        return !empty($this->label) ? $this->label : $this->address->getCompany();
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
