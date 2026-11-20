<?php declare(strict_types=1);

namespace Loki\Components\Util\Security;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class AjaxSignature
{
    private const KEY_PURPOSE = 'loki_components_ajax_signature';

    public function __construct(
        private readonly DeploymentConfig $deploymentConfig,
        private readonly AppState $appState,
        private readonly StoreManagerInterface $storeManager,
    ) {
    }

    public function sign(array $data): string
    {
        return hash_hmac(
            'sha256',
            $this->buildPayload($data),
            $this->getSigningKey()
        );
    }

    public function verify(array $data, string $signature): bool
    {
        $expectedSignature = $this->sign($data);
        return hash_equals($expectedSignature, $signature);
    }

    private function buildPayload(array $data): string
    {
        $data['scope'] = [
            'area' => $this->getAreaCode(),
            'store' => $this->getStoreCode(),
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function getSigningKey(): string
    {
        $cryptKey = (string)$this->deploymentConfig->get('crypt/key');

        return hash_hmac('sha256', self::KEY_PURPOSE, $cryptKey);
    }

    private function getAreaCode(): string
    {
        try {
            return (string)$this->appState->getAreaCode();
        } catch (LocalizedException) {
            return '';
        }
    }

    private function getStoreCode(): string
    {
        try {
            return (string)$this->storeManager->getStore()->getCode();
        } catch (LocalizedException) {
            return '';
        }
    }
}
