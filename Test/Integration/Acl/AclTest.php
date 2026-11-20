<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Acl;

use Magento\Config\Model\Config\Structure;
use Magento\Framework\Acl\AclResource\ProviderInterface;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class AclTest extends TestCase
{
    public function testAclResourcesExist(): void
    {
        $aclResources = ObjectManager::getInstance()->get(ProviderInterface::class)->getAclResources();
        $adminResource = $this->findResource($aclResources, 'Magento_Backend::admin');
        $this->assertNotNull($adminResource);

        $this->assertNotNull($this->findResource($adminResource['children'], 'Loki_Components::index'));

        $configResource = $this->findResourceByPath($adminResource['children'], [
            'Magento_Backend::stores',
            'Magento_Backend::stores_settings',
            'Magento_Config::config',
            'Loki_Components::configuration',
        ]);
        $this->assertNotNull($configResource, 'Resource Loki_Components::configuration not found');
    }

    public function testConfigSectionUsesConfigurationResource(): void
    {
        $section = ObjectManager::getInstance()->get(Structure::class)->getElement('loki_components');
        $this->assertSame('Loki_Components::configuration', $section->getAttribute('resource'));
    }

    private function findResourceByPath(array $resources, array $resourceIds): ?array
    {
        $resource = null;
        foreach ($resourceIds as $resourceId) {
            $resource = $this->findResource($resources, $resourceId);
            if ($resource === null) {
                return null;
            }

            $resources = $resource['children'];
        }

        return $resource;
    }

    private function findResource(array $resources, string $resourceId): ?array
    {
        foreach ($resources as $resource) {
            if ($resource['id'] === $resourceId) {
                return $resource;
            }
        }

        return null;
    }
}
