<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Filter;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Exception\RecursionException;
use Loki\Components\Filter\Filter;

#[AppArea('frontend')]
class FilterTest extends TestCase
{
    public function testDeeplyNestedArrayThrowsRecursionException(): void
    {
        $filter = ObjectManager::getInstance()->get(Filter::class);
        $component = $this->createComponent();

        $this->expectException(RecursionException::class);
        $this->expectExceptionMessage('Too many array levels');

        $filter->filter($component, $this->buildNestedArray(12));
    }

    public function testShallowArrayDoesNotThrow(): void
    {
        $filter = ObjectManager::getInstance()->get(Filter::class);
        $component = $this->createComponent();

        $data = $this->buildNestedArray(3);
        $result = $filter->filter($component, $data);

        $this->assertSame($data, $result);
    }

    private function createComponent(): ComponentInterface
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getFilters')->willReturn([]);

        return $component;
    }

    private function buildNestedArray(int $levels): array
    {
        $value = 'value';
        for ($i = 0; $i < $levels; $i++) {
            $value = [$value];
        }

        return $value;
    }
}
