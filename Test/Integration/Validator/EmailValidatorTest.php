<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Validator;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\Config;
use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\EmailValidator;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

#[AppArea('frontend')]
class EmailValidatorTest extends TestCase
{
    public function testWithNoValue(): void
    {
        $validator = ObjectManager::getInstance()->get(EmailValidator::class);
        $this->assertTrue($validator->validate(null));
    }

    /**
     * @param string $email
     * @param bool $expectedResult
     * @return void
     * @dataProvider getValues
     */
    public function testWithVariousValues(string $email, true|string $expectedResult): void
    {
        $validator = ObjectManager::getInstance()->get(EmailValidator::class);
        $actualResult = $validator->validate($email);
        if (true === $actualResult) {
            $this->assertTrue($actualResult);
            return;
        }

        $error = array_pop($actualResult);
        $this->assertStringContainsString($expectedResult, $error, var_export($actualResult, true));
    }

    public function getValues(): array
    {
        return [
            ['jane@example.com', true],
            ['jane@example', 'Invalid email'],
            ['jane', 'Invalid email'],
            ['jane@example.comcomcomcomcom', 'does not seem to be valid'],
        ];
    }

    #[Config(AccountManagement::GUEST_CHECKOUT_LOGIN_OPTION_SYS_CONFIG, 1, 'website')]
    public function testWithExistingEmail(): void
    {
        $this->createCustomer('john@example.com');

        $validator = ObjectManager::getInstance()->get(EmailValidator::class);
        $email = 'john@example.com';
        $result = $validator->validate($email);

        $this->assertNotTrue($result);
        $this->assertContains('This email address is not available', $result);
    }

    private function createCustomer(string $email)
    {
        $customerRepository = ObjectManager::getInstance()->get(CustomerRepositoryInterface::class);
        try {
            $customerRepository->get($email);
            return;
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
        }

        $customerFactory = ObjectManager::getInstance()->get(CustomerInterfaceFactory::class);
        $customer = $customerFactory->create();
        $customer->setFirstname('John');
        $customer->setLastname('Doe');
        $customer->setEmail($email);
        $customerRepository->save($customer);
    }
}
