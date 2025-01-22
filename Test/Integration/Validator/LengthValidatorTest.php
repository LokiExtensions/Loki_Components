<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Validator;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\Config;
use PHPUnit\Framework\TestCase;
use Yireo\LokiCheckout\Component\Base\Field\FieldViewModel;
use Yireo\LokiComponents\Component\Component;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\ComponentViewModel;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;
use Yireo\LokiComponents\Validator\EmailValidator;
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

        $componentViewModel = $this->createMock(FieldViewModel::class);
        $componentViewModel->method('hasMinLength')->willReturn(true);
        $componentViewModel->method('getMinLength')->willReturn(2);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($componentViewModel);

        $this->assertTrue($validator->validate('foo', $component));
        $this->assertIsArray($validator->validate('f', $component));
    }
}
