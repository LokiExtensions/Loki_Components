<?php declare(strict_types=1);

namespace Loki\Components\Location\Address;

use Magento\Framework\DataObject;

class BusinessHourSegment extends DataObject
{
    public function __construct(
        private readonly string $day,
        private readonly string $openingHour,
        private readonly string $closingHour,
        private readonly string $comment = '',
        array $data = [],
    ) {
        parent::__construct($data);
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function getOpeningHour(): string
    {
        return $this->openingHour;
    }

    public function getClosingHour(): string
    {
        return $this->closingHour;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
