<?php declare(strict_types=1);

namespace Loki\Components\Util\Security;

use Magento\Framework\App\DeploymentConfig;

class AjaxSignature
{
    public function __construct(
        private readonly DeploymentConfig $deploymentConfig
    ) {
    }

    public function sign(array $data): string
    {
        return hash_hmac(
            'sha256',
            $this->buildPayload($data),
            $this->getCryptKey()
        );
    }

    public function verify(array $data, string $signature): bool
    {
        $expectedSignature = $this->sign($data);
        return hash_equals($expectedSignature, $signature);
    }

    private function buildPayload(array $data): string
    {
        return (string)json_encode($data, JSON_UNESCAPED_SLASHES);
    }

    private function getCryptKey(): string
    {
        return (string)$this->deploymentConfig->get('crypt/key');
    }
}
