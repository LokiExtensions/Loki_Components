<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\ViewModel;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\LokiComponents\Component\ComponentInterface;

interface ViewModelInterface extends ArgumentInterface
{
    public function getComponent(): ?ComponentInterface;

    public function setComponent(ComponentInterface $component): void;

    public function hasComponent(): bool;

    public function setBlock(AbstractBlock $block);

    public function getBlock(): AbstractBlock;

    public function hasBlock(): bool;
}
