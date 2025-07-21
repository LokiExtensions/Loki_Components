<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Config;

use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;
use Loki\Components\Config\Config;

class ConfigTest extends TestCase
{
    #[ConfigFixture('loki_components/general/debug', 1)]
    public function testIsDebugEnabled()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertTrue($config->isDebug());
    }

    #[ConfigFixture('loki_components/general/debug', 0)]
    public function testIsDebugDisabled()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertFalse($config->isDebug());
    }

    #[ConfigFixture('loki_components/general/only_validate_ajax', 1)]
    public function testOnlyValidateAjaxEnabled()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertTrue($config->onlyValidateAjax());
    }

    #[ConfigFixture('loki_components/general/only_validate_ajax', 0)]
    public function testOnlyValidateAjaxDisabled()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertFalse($config->onlyValidateAjax());
    }

    #[ConfigFixture('loki_components/global_messages/timeout', 0)]
    public function testGlobalMessagesTimeoutWithEmptyValue()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertSame(0, $config->getGlobalMessagesTimeout());
    }

    #[ConfigFixture('loki_components/global_messages/timeout', 42)]
    public function testGlobalMessagesTimeoutWithPositiveValue()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertSame(42, $config->getGlobalMessagesTimeout());
    }

    #[ConfigFixture('loki_components/global_messages/timeout', -42)]
    public function testGlobalMessagesTimeoutWithNegativeValue()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertSame(0, $config->getGlobalMessagesTimeout());
    }

    #[ConfigFixture('loki_components/validators/enable_mx_validation_for_email', 1)]
    public function testMxValidationForEmailEnabled()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertTrue($config->enableMxValidationForEmail());
    }

    #[ConfigFixture('loki_components/validators/enable_mx_validation_for_email', 0)]
    public function testMxValidationForEmailDisabled()
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertFalse($config->enableMxValidationForEmail());
    }
}
