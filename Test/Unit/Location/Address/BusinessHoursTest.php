<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Location\Address;

use Loki\Components\Location\Address\BusinessHour\Day;
use Loki\Components\Location\Address\BusinessHour\DayFactory;
use Loki\Components\Location\Address\BusinessHour\DayPartFactory;
use Loki\Components\Location\Address\BusinessHours;
use PHPUnit\Framework\TestCase;

class BusinessHoursTest extends TestCase
{
    public function testCreateDayPassesArgumentsToFactory(): void
    {
        $day = new Day($this->createMock(DayPartFactory::class), 'Monday');

        $dayFactory = $this->createMock(DayFactory::class);
        $dayFactory->expects($this->once())
            ->method('create')
            ->with(['label' => 'Monday', 'dayParts' => [], 'comment' => 'Closed for lunch'])
            ->willReturn($day);

        $businessHours = new BusinessHours($dayFactory);
        $this->assertSame($day, $businessHours->createDay('Monday', [], 'Closed for lunch'));
        $this->assertSame([], $businessHours->getDays(), 'Creating a day does not add it');
    }

    public function testAddDayAndSerialize(): void
    {
        $monday = new Day($this->createMock(DayPartFactory::class), 'Monday');
        $tuesday = new Day($this->createMock(DayPartFactory::class), 'Tuesday');

        $businessHours = new BusinessHours($this->createMock(DayFactory::class), ['timezone' => 'UTC']);
        $businessHours->addDay($monday);
        $businessHours->addDay($tuesday);

        $this->assertSame([$monday, $tuesday], $businessHours->getDays());
        $this->assertSame(
            ['days' => [$monday, $tuesday], 'data' => ['timezone' => 'UTC']],
            $businessHours->jsonSerialize()
        );
    }
}
