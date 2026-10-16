<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Validator\LengthValidator;
use Magento\Framework\View\Element\AbstractBlock;
use PHPUnit\Framework\TestCase;

class LengthValidatorTest extends TestCase
{
    use BlockMock;

    public function testCountsCharactersInsteadOfBytes(): void
    {
        $component = $this->getComponent($this->getBlockMock('foo', ['minlength' => 3, 'maxlength' => 3]));

        $validator = new LengthValidator();
        $this->assertTrue($validator->validate('ééé', $component));
        $this->assertTrue($validator->validate('日本語', $component));
        $this->assertIsArray($validator->validate('éééé', $component));
        $this->assertIsArray($validator->validate('éé', $component));
    }

    public function testWithoutComponent(): void
    {
        $this->assertTrue((new LengthValidator())->validate('foo'));
    }

    public function testWithoutBlock(): void
    {
        $this->assertTrue((new LengthValidator())->validate('foo', $this->getComponent(null)));
    }

    private function getComponent(?AbstractBlock $block): ComponentInterface
    {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        return $component;
    }
}
