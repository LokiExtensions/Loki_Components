<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Security;

use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;

class AjaxSignatureTest extends TestCase
{
    private const DATA = [
        'handles' => ['default', 'cms_index_index'],
        'pageHandles' => ['1column'],
        'request' => ['request_uri' => '/foo/bar'],
    ];

    public function testSignIsHmacOfJsonPayloadWithDerivedKey(): void
    {
        $payload = self::DATA;
        $payload['scope'] = ['area' => 'frontend', 'store' => 'default'];
        $key = hash_hmac('sha256', 'loki_components_ajax_signature', 'secret');
        $expected = hash_hmac('sha256', (string)json_encode($payload, JSON_UNESCAPED_SLASHES), $key);

        $this->assertSame($expected, $this->getAjaxSignature('secret')->sign(self::DATA));
    }

    public function testSignatureIsNotMadeWithTheRawCryptKey(): void
    {
        $payload = self::DATA;
        $payload['scope'] = ['area' => 'frontend', 'store' => 'default'];
        $rawSignature = hash_hmac('sha256', (string)json_encode($payload, JSON_UNESCAPED_SLASHES), 'secret');

        $this->assertNotSame($rawSignature, $this->getAjaxSignature('secret')->sign(self::DATA));
    }

    public function testSignIsDeterministic(): void
    {
        $ajaxSignature = $this->getAjaxSignature('secret');
        $this->assertSame($ajaxSignature->sign(self::DATA), $ajaxSignature->sign(self::DATA));
    }

    public function testSignatureDependsOnCryptKey(): void
    {
        $this->assertNotSame(
            $this->getAjaxSignature('secret')->sign(self::DATA),
            $this->getAjaxSignature('other')->sign(self::DATA)
        );
    }

    public function testSignatureDependsOnArea(): void
    {
        $this->assertNotSame(
            $this->getAjaxSignature('secret', 'frontend')->sign(self::DATA),
            $this->getAjaxSignature('secret', 'adminhtml')->sign(self::DATA)
        );
    }

    public function testSignatureDependsOnStore(): void
    {
        $this->assertNotSame(
            $this->getAjaxSignature('secret', 'frontend', 'default')->sign(self::DATA),
            $this->getAjaxSignature('secret', 'frontend', 'nl')->sign(self::DATA)
        );
    }

    public function testSignWithoutAreaCode(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{64}$/',
            $this->getAjaxSignature('secret', null)->sign(self::DATA)
        );
    }

    public function testSignFailsOnInvalidUtf8(): void
    {
        $this->expectException(\JsonException::class);
        $this->getAjaxSignature('secret')->sign(['request' => ['params' => ['x' => "\xFF"]]]);
    }

    public function testVerify(): void
    {
        $ajaxSignature = $this->getAjaxSignature('secret');
        $signature = $ajaxSignature->sign(self::DATA);

        $this->assertTrue($ajaxSignature->verify(self::DATA, $signature));
        $this->assertFalse($ajaxSignature->verify(self::DATA, ''));
        $this->assertFalse($ajaxSignature->verify(self::DATA, str_repeat('0', 64)));

        $tamperedData = self::DATA;
        $tamperedData['handles'][] = 'customer_account';
        $this->assertFalse($ajaxSignature->verify($tamperedData, $signature));
    }

    private function getAjaxSignature(
        string $cryptKey,
        ?string $areaCode = 'frontend',
        string $storeCode = 'default'
    ): AjaxSignature {
        $deploymentConfig = $this->createMock(DeploymentConfig::class);
        $deploymentConfig->method('get')->with('crypt/key')->willReturn($cryptKey);

        $appState = $this->createMock(AppState::class);
        if ($areaCode === null) {
            $appState->method('getAreaCode')->willThrowException(new LocalizedException(new Phrase('No area')));
        } else {
            $appState->method('getAreaCode')->willReturn($areaCode);
        }

        $store = $this->createMock(StoreInterface::class);
        $store->method('getCode')->willReturn($storeCode);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        return new AjaxSignature($deploymentConfig, $appState, $storeManager);
    }
}
