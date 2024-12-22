<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

interface ComponentInterface
{
    public function getViewModel(): ViewModelInterface;
    public function getMutator(): MutatorInterface;
    public function getSourceBlock(): AbstractBlock;
    public function getTargetBlocks(): array;
}
