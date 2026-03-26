<?php declare(strict_types=1);

namespace Loki\Components\Location\Address\BusinessHour;

use Magento\Framework\DataObject;

class DayPart extends DataObject implements \JsonSerializable
{
    public function __construct(
        private readonly string $openingHour,
        private readonly string $closingHour,
        array $data = [],
    ) {
        parent::__construct($data);
    }

    public function getId(): string
    {
        return $this->getOpeningHour().'-'.$this->getClosingHour();
    }

    public function getOpeningHour(): string
    {
        return $this->openingHour;
    }

    public function getClosingHour(): string
    {
        return $this->closingHour;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'openingHour' => $this->getOpeningHour(),
            'closingHour' => $this->getClosingHour(),
            'data' => $this->getData(),
        ];
    }
}
