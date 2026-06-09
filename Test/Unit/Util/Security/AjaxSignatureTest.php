<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Security;

use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\DeploymentConfig;
use PHPUnit\Framework\TestCase;

class AjaxSignatureTest extends TestCase
{
    public function testSignedPayloadVerifiesSuccessfully(): void
    {
        $ajaxSignature = $this->getAjaxSignature();

        $handles = ['default', 'catalog_product_view'];
        $pageHandles = ['1column'];
        $request = ['route_name' => 'catalog', 'action_name' => 'view'];

        $signature = $ajaxSignature->sign($handles, $pageHandles, $request);

        $this->assertNotEmpty($signature);
        $this->assertTrue($ajaxSignature->verify($handles, $pageHandles, $request, $signature));
    }

    public function testVerifyFailsWhenHandlesAreTampered(): void
    {
        $ajaxSignature = $this->getAjaxSignature();

        $signature = $ajaxSignature->sign(['default'], ['1column'], ['route_name' => 'catalog']);

        $this->assertFalse(
            $ajaxSignature->verify(['default', 'evil_handle'], ['1column'], ['route_name' => 'catalog'], $signature)
        );
    }

    public function testVerifyFailsWhenPageHandlesAreTampered(): void
    {
        $ajaxSignature = $this->getAjaxSignature();

        $signature = $ajaxSignature->sign(['default'], ['1column'], ['route_name' => 'catalog']);

        $this->assertFalse(
            $ajaxSignature->verify(['default'], ['2columns-left'], ['route_name' => 'catalog'], $signature)
        );
    }

    public function testVerifyFailsWhenRequestIsTampered(): void
    {
        $ajaxSignature = $this->getAjaxSignature();

        $signature = $ajaxSignature->sign(['default'], ['1column'], ['route_name' => 'catalog']);

        $this->assertFalse(
            $ajaxSignature->verify(['default'], ['1column'], ['route_name' => 'admin'], $signature)
        );
    }

    public function testVerifyFailsWithEmptySignature(): void
    {
        $ajaxSignature = $this->getAjaxSignature();

        $this->assertFalse($ajaxSignature->verify(['default'], ['1column'], [], ''));
    }

    private function getAjaxSignature(string $cryptKey = 'unit-test-crypt-key'): AjaxSignature
    {
        $deploymentConfig = $this->createMock(DeploymentConfig::class);
        $deploymentConfig->method('get')->with('crypt/key')->willReturn($cryptKey);

        return new AjaxSignature($deploymentConfig);
    }
}
