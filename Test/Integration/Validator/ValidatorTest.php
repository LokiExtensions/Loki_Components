<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Exception\RecursionException;
use Loki\Components\Validator\Validator;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class ValidatorTest extends TestCase
{
    public function testThrowsRecursionExceptionWhenDepthExceeded(): void
    {
        $validator = ObjectManager::getInstance()->get(Validator::class);
        $component = $this->createMock(ComponentInterface::class);

        $this->expectException(RecursionException::class);
        $this->expectExceptionMessage('Too many array levels');

        $validator->validate($component, $this->buildNestedArray(12));
    }

    public function testDoesNotThrowForShallowData(): void
    {
        $validator = ObjectManager::getInstance()->get(Validator::class);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getValidators')->willReturn([]);

        $this->assertTrue($validator->validate($component, 'foo'));
        $this->assertTrue($validator->validate($component, ['foo', 'bar']));
    }

    public function testDepthArgumentTriggersRecursionExceptionDirectly(): void
    {
        $validator = ObjectManager::getInstance()->get(Validator::class);
        $component = $this->createMock(ComponentInterface::class);

        $this->expectException(RecursionException::class);
        $this->expectExceptionMessage('Too many array levels');

        $validator->validate($component, null, '', 10);
    }

    private function buildNestedArray(int $levels): array
    {
        $data = ['value'];
        for ($i = 0; $i < $levels; $i++) {
            $data = [$data];
        }

        return $data;
    }
}
