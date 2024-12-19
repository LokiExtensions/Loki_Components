<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Factory;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RuntimeException;

class ViewModelFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(string $viewModelClass): ArgumentInterface
    {
        $viewModel = $this->objectManager->get($viewModelClass);
        if (empty($viewModel)) {
            throw new RuntimeException('ViewModel "'.$viewModelClass.'" could not be instantiated');
        }

        if (false === $viewModel instanceof ArgumentInterface) {
            throw new RuntimeException('Class "'.$viewModelClass.'" is not a ViewModel');
        }

        return $viewModel;
    }
}
