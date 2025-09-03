<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Validator\Dummy;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Test\Integration\Dummy\LengthViewModelDummy;
use Loki\Components\Validator\LengthValidator;

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

        $block = ObjectManager::getInstance()->create(Template::class);
        $block->setData('minlength', 2);

        $componentViewModel = $this->createMock(LengthViewModelDummy::class);
        $componentViewModel->method('getBlock')->willReturn($block);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($componentViewModel);

        $this->assertTrue($validator->validate('foo', $component));
        $this->assertIsArray($validator->validate('f', $component));
    }
}
