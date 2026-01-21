<?php declare(strict_types=1);

namespace Loki\Components\Factory;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RuntimeException;

class ViewModelFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function get(string $viewModelClass): ArgumentInterface
    {
        $viewModel = $this->objectManager->get($viewModelClass);
        $this->validateViewModel($viewModel, $viewModelClass);
        return $viewModel;
    }

    public function create(string $viewModelClass, array $data): ArgumentInterface
    {
        $viewModel = $this->objectManager->create($viewModelClass, $data);
        $this->validateViewModel($viewModel, $viewModelClass);
        return $viewModel;
    }

    private function validateViewModel(mixed $viewModel, string $viewModelClass): void
    {
        if (empty($viewModel)) {
            throw new RuntimeException('ViewModel "' . $viewModelClass . '" could not be instantiated');
        }

        if (false === $viewModel instanceof ArgumentInterface) {
            throw new RuntimeException('Class "' . $viewModelClass . '" is not a ViewModel');
        }
    }
}
