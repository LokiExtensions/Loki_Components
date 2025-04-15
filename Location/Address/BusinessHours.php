<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Location\Address;

class BusinessHours
{
    private array $businessHourSegments = [];

    public function __construct(
        private BusinessHourSegmentFactory $businessHourSegmentFactory
    ) {
    }

    public function addSegment(BusinessHourSegment $businessHourSegment): void
    {
        $this->businessHourSegments[] = $businessHourSegment;
    }

    public function add(string $day, string $openingHour, string $closingHour): void
    {
        $this->businessHourSegments[] = $this->businessHourSegmentFactory->create([
            'day' => $day,
            'openingHour' => $openingHour,
            'closingHour' => $closingHour,
        ]);
    }

    /**
     * @return BusinessHourSegment[]
     */
    public function get(): array
    {
        return $this->businessHourSegments;
    }
}
