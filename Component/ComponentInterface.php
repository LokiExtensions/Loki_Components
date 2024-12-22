<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

interface ComponentInterface
{
    public function getName(): string;
    public function getSourceBlock(): string;
    public function getViewModel(): ?ViewModelInterface;
    public function getTargetBlocks(): array;
}
