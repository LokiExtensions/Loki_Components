<?php declare(strict_types=1);

namespace Loki\Components\Util\Security;

use Magento\Framework\App\DeploymentConfig;

class AjaxSignature
{
    public function __construct(
        private readonly DeploymentConfig $deploymentConfig
    ) {
    }

    public function sign(array $handles, array $pageHandles, array $request): string
    {
        return hash_hmac(
            'sha256',
            $this->buildPayload($handles, $pageHandles, $request),
            $this->getCryptKey()
        );
    }

    public function verify(array $handles, array $pageHandles, array $request, string $signature): bool
    {
        $expectedSignature = $this->sign($handles, $pageHandles, $request);
        return hash_equals($expectedSignature, $signature);
    }

    private function buildPayload(array $handles, array $pageHandles, array $request): string
    {
        return (string)json_encode([$handles, $pageHandles, $request], JSON_UNESCAPED_SLASHES);
    }

    private function getCryptKey(): string
    {
        return (string)$this->deploymentConfig->get('crypt/key');
    }
}
