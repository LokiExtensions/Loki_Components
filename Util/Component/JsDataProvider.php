<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\LokiComponents\Component\ComponentInterface;
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
        private CamelCaseConvertor $camelCaseConvertor
    ) {
    }

    // @todo: Rename to getJsData()
    public function getData(ComponentViewModelInterface $viewModel): array
    {
        $data = [];
        $block = $viewModel->getBlock();

        $data['title'] = $this->getComponentTitle($viewModel);
        $data['name'] = $this->getComponentName($viewModel);
        $data['value'] = $viewModel->getValue();
        $data['blockId'] = $block->getNameInLayout();
        $data['target'] = $this->getTargets($viewModel);

        try {
            $data['elementId'] = $this->componentUtil->getElementIdByBlockName($block->getNameInLayout());
        } catch (\RuntimeException $e) {
        }

        $validators = [];
        foreach ($viewModel->getValidators() as $validator) {
            $validators[] = $validator;
        }

        $data['validators'] = $validators;

        $filters = [];
        foreach ($viewModel->getFilters() as $filter) {
            $filters[] = $filter;
        }

        $data['filters'] = $filters;

        // @doc
        $viewModelData = $viewModel->getJsData();
        if (is_array($viewModelData)) {
            $data = array_merge($data, $viewModelData);
        }

        // @doc
        $blockData = $block->getJsData();
        if (is_array($blockData)) {
            $data = array_merge($data, $blockData);
        }

        return $data;
    }

    public function getJson(ComponentViewModelInterface $viewModel): string
    {
        return json_encode($this->getData($viewModel));
    }

    public function getComponentName(ComponentViewModelInterface $componentViewModel): string
    {
        $block = $componentViewModel->getBlock();
        $componentName = $block->getJsComponentName();
        if (!empty($componentName)) {
            return $componentName;
        }

        if ($componentViewModel instanceof ComponentViewModelInterface) {
            $componentName = $componentViewModel->getJsComponentName();
            if (!empty($componentName)) {
                return $componentName;
            }
        }

        return 'LokiComponent';
    }

    public function getComponentTitle(ComponentViewModelInterface $componentViewModel): string
    {
        $block = $componentViewModel->getBlock();
        return $this->camelCaseConvertor->toCamelCase($block->getNameInLayout());
    }

    public function getJsValue(string $value): string
    {
        return str_replace("\n", '\n', $value); // @todo: Is this good enough?
    }

    private function getTargets(ComponentViewModelInterface $viewModel): string
    {
        $targetNames = [
            ...$viewModel->getTargets(),
            $viewModel->getBlock()->getNameInLayout(),
        ];

        $targets = (array)$viewModel->getBlock()->getTargets();
        foreach ($targets as $target) {
            $targetNames[] = $target;
        }

        $targetNames = $this->convertDomIds($targetNames);
        $targetNames = array_unique($targetNames);

        return implode(' ', $targetNames);
    }

    private function convertDomIds(array $domIds): array
    {
        foreach ($domIds as $domIdIndex => $domId) {
            $domIds[$domIdIndex] = $this->idConvertor->toElementId($domId);
        }

        return $domIds;
    }
}
