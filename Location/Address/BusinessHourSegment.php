<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Location\Address;
class BusinessHourSegment
{
    public function __construct(
        private string $day,
        private string $openingHour,
        private string $closingHour,
    ) {
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
}
