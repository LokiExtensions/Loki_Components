<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Config\XmlConfig;
use Loki\Components\Util\DefaultTargets;

class XmlConfigTest extends TestCase
{
    public function testGlobalDefinitionsAreExtendedByFrontend(): void
    {
        $globalData = [
            'groups' => [
                'default' => $this->group('default'),
            ],
            'components' => [
                'loki.global' => $this->component('loki.global'),
            ],
        ];

        $frontendData = [
            'groups' => [],
            'components' => [
                'loki.frontend' => $this->component('loki.frontend'),
            ],
        ];

        $xmlConfig = $this->createXmlConfig($globalData, $frontendData);
        $definitions = $xmlConfig->getComponentDefinitions();

        $this->assertArrayHasKey('loki.global', $definitions);
        $this->assertArrayHasKey('loki.frontend', $definitions);
    }

    public function testFrontendComponentResolvesGlobalGroup(): void
    {
        $globalData = [
            'groups' => [
                'default' => $this->group('default', viewModel: 'Some\Global\ViewModel'),
            ],
            'components' => [],
        ];

        $frontendData = [
            'groups' => [],
            'components' => [
                'loki.frontend' => $this->component('loki.frontend', group: 'default'),
            ],
        ];

        $xmlConfig = $this->createXmlConfig($globalData, $frontendData);
        $definitions = $xmlConfig->getComponentDefinitions();

        $this->assertArrayHasKey('loki.frontend', $definitions);
        $this->assertSame('Some\Global\ViewModel', $definitions['loki.frontend']->getViewModelClass());
    }

    public function testFrontendOverridesGlobalComponentAttribute(): void
    {
        $globalData = [
            'groups' => ['default' => $this->group('default')],
            'components' => [
                'loki.shared' => $this->component('loki.shared', viewModel: 'Global\ViewModel'),
            ],
        ];

        $frontendData = [
            'groups' => [],
            'components' => [
                'loki.shared' => $this->component('loki.shared', viewModel: 'Frontend\ViewModel'),
            ],
        ];

        $xmlConfig = $this->createXmlConfig($globalData, $frontendData);
        $definitions = $xmlConfig->getComponentDefinitions();

        $this->assertSame('Frontend\ViewModel', $definitions['loki.shared']->getViewModelClass());
    }

    public function testFrontendCanDisableGlobalValidatorByName(): void
    {
        $globalData = [
            'groups' => ['default' => $this->group('default')],
            'components' => [
                'loki.shared' => $this->component('loki.shared', validators: [
                    'required' => ['name' => 'required', 'disabled' => false],
                ]),
            ],
        ];

        $frontendData = [
            'groups' => [],
            'components' => [
                'loki.shared' => $this->component('loki.shared', validators: [
                    'required' => ['name' => 'required', 'disabled' => true],
                ]),
            ],
        ];

        $xmlConfig = $this->createXmlConfig($globalData, $frontendData);
        $definitions = $xmlConfig->getComponentDefinitions();

        $this->assertNotContains('required', $definitions['loki.shared']->getValidators());
    }

    public function testGroupDefaultsFallBackToBaseClasses(): void
    {
        $globalData = [
            'groups' => ['default' => $this->group('default')],
            'components' => [
                'loki.plain' => $this->component('loki.plain'),
            ],
        ];

        $xmlConfig = $this->createXmlConfig($globalData, ['groups' => [], 'components' => []]);
        $definitions = $xmlConfig->getComponentDefinitions();

        $this->assertSame(Component::class, $definitions['loki.plain']->getClassName());
        $this->assertSame(ComponentViewModel::class, $definitions['loki.plain']->getViewModelClass());
    }

    private function group(
        string $name,
        string $class = '',
        string $context = '',
        string $viewModel = '',
        string $repository = '',
        array $targets = []
    ): array {
        return [
            'name' => $name,
            'class' => $class,
            'context' => $context,
            'viewModel' => $viewModel,
            'repository' => $repository,
            'targets' => $targets,
        ];
    }

    private function component(
        string $name,
        string $group = 'default',
        string $class = '',
        string $context = '',
        string $viewModel = '',
        string $repository = '',
        array $targets = [],
        array $validators = [],
        array $filters = []
    ): array {
        return [
            'name' => $name,
            'group' => $group,
            'class' => $class,
            'context' => $context,
            'viewModel' => $viewModel,
            'repository' => $repository,
            'targets' => $targets,
            'validators' => $validators,
            'filters' => $filters,
        ];
    }

    private function createXmlConfig(array $globalData, array $frontendData): XmlConfig
    {
        $reader = $this->createMock(ReaderInterface::class);
        $reader->method('read')->willReturnCallback(
            static function ($scope) use ($globalData, $frontendData) {
                return $scope === 'frontend' ? $frontendData : $globalData;
            }
        );

        $configScope = $this->createMock(ScopeInterface::class);
        $configScope->method('getCurrentScope')->willReturn('frontend');

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('load')->willReturn(false);
        $cache->method('save')->willReturn(true);

        $serializer = $this->createMock(SerializerInterface::class);

        $defaultTargets = new DefaultTargets([]);

        return new XmlConfig(
            $reader,
            $configScope,
            $cache,
            $defaultTargets,
            'loki_components',
            $serializer
        );
    }
}
