<?php declare(strict_types=1);

namespace Loki\Components\Util\Component;

use Loki\Components\Util\Block\GetElementId;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\CamelCaseConvertor;
use Loki\Components\Util\ComponentUtil;
use Loki\Components\Util\IdConvertor;

class JsDataProvider implements ArgumentInterface
{
    public function __construct(
        private readonly ComponentUtil $componentUtil,
        private readonly GetElementId $getElementId,
        private readonly IdConvertor $idConvertor,
        private readonly CamelCaseConvertor $camelCaseConvertor,
    ) {
    }

    public function getComponentData(ComponentInterface $component): array
    {
        $data = [];
        $data['id'] = $this->getComponentId($component);
        $data['name'] = $this->getComponentName($component);
        $data['targets'] = $this->getTargets($component);

        $block = $component->getBlock();
        $data['blockId'] = $block->getNameInLayout();
        $data['elementId'] = $this->getElementId->execute($block);
        $data['validators'] = $component->getValidators();
        $data['filters'] = $component->getFilters();

        $viewModel = $component->getViewModel();
        $viewModelData = $viewModel->getJsData();
        if ($viewModelData !== []) {
            $data = array_merge($data, $viewModelData);
        }

        // @doc
        $blockData = $block->getJsData();
        if (is_array($blockData)) {
            $data = array_merge($data, $blockData);
        }

        return $data;
    }

    public function getComponentName(ComponentInterface $component): string
    {
        $block = $component->getBlock();
        $componentName = $block->getJsComponentName();
        if (!empty($componentName)) {
            return $componentName;
        }

        $viewModel = $component->getViewModel();
        $componentName = $viewModel->getJsComponentName();
        if ($componentName !== null && $componentName !== '' && $componentName !== '0') {
            return $componentName;
        }

        return 'LokiComponent';
    }

    public function getComponentId(ComponentInterface $component): string
    {
        $block = $component->getBlock();

        return $this->camelCaseConvertor->toCamelCase($block->getNameInLayout());
    }

    private function getTargets(ComponentInterface $component): array
    {
        $block = $component->getBlock();
        $targetNames = (array)$block->getTargets();

        $viewModel = $component->getViewModel();
        $targetNames = array_merge($targetNames, (array)$viewModel->getTargets());

        $targetNames = $this->convertDomIds($targetNames);
        $targetNames = array_unique($targetNames);

        return array_values($targetNames);
    }

    private function convertDomIds(array $domIds): array
    {
        foreach ($domIds as $domIdIndex => $domId) {
            $domIds[$domIdIndex] = $this->idConvertor->toElementId($domId);
        }

        return $domIds;
    }
}
