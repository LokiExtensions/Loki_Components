<?php declare(strict_types=1);

namespace Loki\Components\Location\Address;

class BusinessHours
{
    private array $businessHourSegments = [];

    public function __construct(
        private readonly BusinessHourSegmentFactory $businessHourSegmentFactory
    ) {
    }

    public function addSegment(BusinessHourSegment $businessHourSegment): void
    {
        $this->businessHourSegments[] = $businessHourSegment;
    }

    public function add(string $day, string $openingHour = '', string $closingHour = '', string $comment = ''): void
    {
        $this->businessHourSegments[] = $this->businessHourSegmentFactory->create([
            'day' => $day,
            'openingHour' => $openingHour,
            'closingHour' => $closingHour,
            'comment' => $comment,
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
