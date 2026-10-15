<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Security;

use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\DeploymentConfig;
use PHPUnit\Framework\TestCase;

class AjaxSignatureTest extends TestCase
{
    private const DATA = [
        'handles' => ['default', 'cms_index_index'],
        'pageHandles' => ['1column'],
        'request' => ['request_uri' => '/foo/bar'],
    ];

    public function testSignIsHmacOfJsonPayload(): void
    {
        $expected = hash_hmac('sha256', (string)json_encode(self::DATA, JSON_UNESCAPED_SLASHES), 'secret');
        $this->assertSame($expected, $this->getAjaxSignature('secret')->sign(self::DATA));
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

    private function getAjaxSignature(string $cryptKey): AjaxSignature
    {
        $deploymentConfig = $this->createMock(DeploymentConfig::class);
        $deploymentConfig->method('get')->with('crypt/key')->willReturn($cryptKey);

        return new AjaxSignature($deploymentConfig);
    }
}
