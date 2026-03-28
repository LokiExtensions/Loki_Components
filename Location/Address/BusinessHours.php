<?php declare(strict_types=1);

namespace Loki\Components\Location\Address;

use JsonSerializable;
use Loki\Components\Location\Address\BusinessHour\Day;
use Loki\Components\Location\Address\BusinessHour\DayFactory;
use Magento\Framework\DataObject;

class BusinessHours extends DataObject implements JsonSerializable
{
    private array $days = [];

    public function __construct(
        private readonly DayFactory $dayFactory,
        array $data = [],
    ) {
        parent::__construct($data);
    }

    public function addDay(Day $day): void
    {
        $this->days[] = $day;
    }

    public function createDay(string $day, array $dayParts = [], string $comment = ''): Day
    {
        return $this->dayFactory->create([
            'label' => $day,
            'dayParts' => $dayParts,
            'comment' => $comment,
        ]);
    }

    /**
     * @return Day[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
        return [
            'days' => $this->getDays(),
            'data' => $this->getData(),
        ];
    }
}
