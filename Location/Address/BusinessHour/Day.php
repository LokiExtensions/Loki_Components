<?php declare(strict_types=1);

namespace Loki\Components\Location\Address\BusinessHour;

use JsonSerializable;
use Magento\Framework\DataObject;

class Day extends DataObject implements JsonSerializable
{
    public function __construct(
        private readonly DayPartFactory $dayPartFactory,
        private readonly string $label,
        private array $dayParts = [],
        private readonly string $comment = '',
        array $data = [],
    ) {
        parent::__construct($data);
    }

    public function addDayPart(string $openingHour, string $closingHour): void
    {
        $this->dayParts[] = $this->dayPartFactory->create([
            'openingHour' => $openingHour,
            'closingHour' => $closingHour,
        ]);
    }

    public function getId(): string
    {
        return strtolower($this->getLabel());
    }

    public function getLabel(): string
    {
        return (string)__($this->label);
    }

    public function getDayParts(): array
    {
        return $this->dayParts;
    }

    public function getComment(): string
    {
        return (string)__($this->comment);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'dayParts' => $this->getDayParts(),
            'comment' => $this->getComment(),
        ];
    }
}
