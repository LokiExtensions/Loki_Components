<?php declare(strict_types=1);

namespace Loki\Components\Location;

use JsonSerializable;
use Loki\Components\Location\Address\BusinessHours;
use Magento\Framework\DataObject;

class Location extends DataObject implements JsonSerializable
{
    public function __construct(
        private readonly Address $address,
        private readonly ?BusinessHours $businessHours = null,
        private readonly string $id = '',
        private readonly string $label = '',
        private readonly array $value = [],
        private readonly string $distance = '',
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

    public function getLabel(): string
    {
        return !empty($this->label) ? $this->label : $this->address->getCompany();
    }

    public function getDistance(): string
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

    public function getValue(): array
    {
        return $this->value;
    }

    public function getAttributes(): array
    {
        $attributes = [
            'address' => $this->getAddress(),
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'distance' => $this->getDistance(),
            'pickupFrom' => $this->getPickupFrom(),
            'businessHours' => $this->getBusinessHours(),
            'data' => $this->getData(),
        ];

        return $attributes;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
        return $this->getAttributes();
    }

}
