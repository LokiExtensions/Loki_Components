<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Validator\Dummy;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Test\Integration\Dummy\LengthViewModelDummy;
use Yireo\LokiComponents\Validator\LengthValidator;

#[AppArea('frontend')]
class LengthValidatorTest extends TestCase
{
    public function testWithNoValue(): void
    {
        $validator = ObjectManager::getInstance()->get(LengthValidator::class);
        $this->assertTrue($validator->validate(0));
    }

    public function testWithVariousValues(): void
    {
        $validator = ObjectManager::getInstance()->get(LengthValidator::class);

        $componentViewModel = $this->createMock(LengthViewModelDummy::class);
        $componentViewModel->method('hasMinLength')->willReturn(true);
        $componentViewModel->method('getMinLength')->willReturn(2);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($componentViewModel);

        $this->assertTrue($validator->validate('foo', $component));
        $this->assertIsArray($validator->validate('f', $component));
    }
}
