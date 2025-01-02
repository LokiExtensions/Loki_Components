<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RuntimeException;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Filter\FilterRegistry;
use Yireo\LokiComponents\Filter\Security;
use Yireo\LokiComponents\Util\CamelCaseConvertor;
use Yireo\LokiComponents\Util\IdConvertor;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;
use Yireo\LokiComponents\Util\ComponentUtil;

// @todo: Retrieve targets from Component-class instead of ViewModel-class
// @todo: Remove this and replace with JsDataProvider
class JsDataProvider implements ArgumentInterface
{
    public function __construct(
        private ComponentUtil $componentUtil,
        private IdConvertor $idConvertor,
        private CamelCaseConvertor $camelCaseConvertor,
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
        $data['elementId'] = $this->componentUtil->getElementIdByBlockName($block->getNameInLayout());

        $data['validators'] = $component->getValidators();
        $data['filters'] = $component->getFilters();
        $data['messages'] = $component->getLocalMessageManager()->toArray();

        $viewModel = $component->getViewModel();
        if ($viewModel instanceof ComponentViewModelInterface) {
            $data['value'] = $viewModel->getValue();

            // @doc
            $viewModelData = $viewModel->getJsData();
            if (is_array($viewModelData)) {
                $data = array_merge($data, $viewModelData);
            }
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
        if ($viewModel instanceof ComponentViewModelInterface) {
            $componentName = $viewModel->getJsComponentName();
            if (!empty($componentName)) {
                return $componentName;
            }
        }

        return 'LokiComponent';
    }

    public function getComponentId(ComponentInterface $component): string
    {
        $block = $component->getBlock();

        return $this->camelCaseConvertor->toCamelCase($block->getNameInLayout());
    }

    // @todo: Where is this being used?
    public function getJsValue(mixed $value): mixed
    {
        return str_replace("\n", '\n', (string)$value);
    }

    private function getTargets(ComponentInterface $component): array
    {
        $block = $component->getBlock();
        $targetNames = (array)$block->getTargets();

        $viewModel = $component->getViewModel();
        if ($viewModel instanceof ComponentViewModelInterface) {
            $targetNames = array_merge($targetNames, (array)$viewModel->getTargets());
        }

        $targetNames = $this->convertDomIds($targetNames);
        $targetNames = array_values($targetNames);
        return array_unique($targetNames);
    }

    private function convertDomIds(array $domIds): array
    {
        foreach ($domIds as $domIdIndex => $domId) {
            $domIds[$domIdIndex] = $this->idConvertor->toElementId($domId);
        }

        return $domIds;
    }
}
