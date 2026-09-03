<?php
declare(strict_types=1);

namespace Loki\Components\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data\Scoped as ScopedDataConfig;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;
use RuntimeException;
use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Config\XmlConfig\Definition\ComponentDefinition;
use Loki\Components\Util\DefaultTargets;

class XmlConfig extends ScopedDataConfig
{
    /**
     * @var string[]
     */
    protected $_scopePriorityScheme = ['global'];

    private array $componentDefinitions = [];

    public function __construct(
        ReaderInterface $reader,
        ScopeInterface $configScope,
        CacheInterface $cache,
        private readonly DefaultTargets $defaultTargets,
        $cacheId,
        ?SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId, $serializer);
    }

    /**
     * @return ComponentDefinition[]
     */
    public function getComponentDefinitions(): array
    {
        if (!empty($this->componentDefinitions)) {
            return $this->componentDefinitions;
        }

        $groups = (array)$this->get('groups');
        $componentsData = (array)$this->get('components');
        foreach ($componentsData as $componentData) {
            if (empty($componentData)) {
                continue;
            }

            $name = $componentData['name'];
            $this->componentDefinitions[$name] = $this->createComponentDefinition($componentData, $groups);
        }

        return $this->componentDefinitions;
    }

    /**
     * @param array $componentData
     * @param array $groups
     *
     * @return ComponentDefinition
     */
    private function createComponentDefinition(array $componentData, array $groups): ComponentDefinition
    {
        $group = $this->resolveGroup($componentData, $groups);

        return new ComponentDefinition(
            $componentData['name'],
            $this->resolveClass($componentData, $group),
            $this->resolveValue('context', $componentData, $group),
            $this->resolveViewModel($componentData, $group),
            $this->resolveValue('repository', $componentData, $group),
            $this->resolveTargets($componentData, $group),
            $this->resolveNames($componentData['validators'] ?? []),
            $this->resolveNames($componentData['filters'] ?? []),
        );
    }

    private function resolveGroup(array $componentData, array $groups): array
    {
        $groupName = $componentData['group'] ?? 'default';
        if ($groupName === '' || $groupName === '0') {
            $groupName = 'default';
        }

        if (false === array_key_exists($groupName, $groups)) {
            throw new RuntimeException(
                'Component "' . $componentData['name'] . '" refers to unknown group "' . $groupName . '"'
            );
        }

        return $groups[$groupName];
    }

    private function resolveClass(array $componentData, array $group): string
    {
        $componentClass = (string)($componentData['class'] ?? '');
        if ($componentClass !== '' && $componentClass !== '0') {
            return $componentClass;
        }

        $groupClass = (string)($group['class'] ?? '');
        if ($groupClass !== '' && $groupClass !== '0') {
            return $groupClass;
        }

        return Component::class;
    }

    private function resolveViewModel(array $componentData, array $group): string
    {
        $componentViewModel = (string)($componentData['viewModel'] ?? '');
        if ($componentViewModel !== '' && $componentViewModel !== '0') {
            return $componentViewModel;
        }

        $groupViewModel = (string)($group['viewModel'] ?? '');
        if ($groupViewModel !== '' && $groupViewModel !== '0') {
            return $groupViewModel;
        }

        return ComponentViewModel::class;
    }

    private function resolveValue(string $key, array $componentData, array $group): string
    {
        $componentValue = (string)($componentData[$key] ?? '');
        if ($componentValue !== '' && $componentValue !== '0') {
            return $componentValue;
        }

        return (string)($group[$key] ?? '');
    }

    private function resolveTargets(array $componentData, array $group): array
    {
        $blockName = (string)$componentData['name'];
        $targets = [];
        $disabledTargets = [];

        $targets[] = $blockName;

        $mergedTargets = array_merge(
            $group['targets'] ?? [],
            $componentData['targets'] ?? []
        );

        foreach ($mergedTargets as $target) {
            $targetName = (string)$target['name'];
            if ($targetName === 'self') {
                $targetName = $blockName;
            }

            if (!empty($target['disabled'])) {
                $disabledTargets[] = $targetName;
            } else {
                $targets[] = $targetName;
            }
        }

        $targets = array_merge($targets, $this->defaultTargets->getTargets());
        $targets = array_diff($targets, $disabledTargets);

        return array_values(array_unique($targets));
    }

    private function resolveNames(array $items): array
    {
        $names = [];
        foreach ($items as $item) {
            if (!empty($item['disabled'])) {
                continue;
            }

            $names[] = (string)$item['name'];
        }

        return array_values(array_unique($names));
    }
}
