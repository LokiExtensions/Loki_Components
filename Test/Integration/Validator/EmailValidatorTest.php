<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Validator;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Validator\EmailValidator;

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
}
