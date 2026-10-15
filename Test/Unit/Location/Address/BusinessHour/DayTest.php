<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Location\Address\BusinessHour;

use Loki\Components\Location\Address\BusinessHour\Day;
use Loki\Components\Location\Address\BusinessHour\DayPart;
use Loki\Components\Location\Address\BusinessHour\DayPartFactory;
use PHPUnit\Framework\TestCase;

class DayTest extends TestCase
{
    public function testGetters(): void
    {
        $day = new Day($this->createMock(DayPartFactory::class), 'Monday', [], 'Closed for lunch');

        $this->assertSame('monday', $day->getId());
        $this->assertSame('Monday', $day->getLabel());
        $this->assertSame('Closed for lunch', $day->getComment());
        $this->assertSame([], $day->getDayParts());
    }

    public function testAddDayPartAndSerialize(): void
    {
        $dayPartFactory = $this->createMock(DayPartFactory::class);
        $dayPartFactory->method('create')->willReturnCallback(
            fn (array $arguments) => new DayPart($arguments['openingHour'], $arguments['closingHour'])
        );

        $day = new Day($dayPartFactory, 'Monday');
        $day->addDayPart('09:00', '12:00');
        $day->addDayPart('13:00', '17:00');

        $dayParts = $day->getDayParts();
        $this->assertCount(2, $dayParts);
        $this->assertSame('09:00-12:00', $dayParts[0]->getId());
        $this->assertSame('13:00', $dayParts[1]->getOpeningHour());
        $this->assertSame('17:00', $dayParts[1]->getClosingHour());

        $this->assertSame(
            '{"id":"monday","label":"Monday","dayParts":['
            . '{"id":"09:00-12:00","openingHour":"09:00","closingHour":"12:00","data":[]},'
            . '{"id":"13:00-17:00","openingHour":"13:00","closingHour":"17:00","data":[]}'
            . '],"comment":""}',
            json_encode($day)
        );
    }
}
