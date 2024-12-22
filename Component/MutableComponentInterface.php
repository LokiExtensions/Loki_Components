<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Component\Mutator\MutatorInterface;
use Yireo\LokiComponents\Component\ViewModel\ViewModelInterface;

interface MutableComponentInterface extends ComponentInterface
{
    public function getViewModel(): ViewModelInterface;

    public function getMutator(): MutatorInterface;

    public function getValidators(): array;

    public function getFilters(): array;

    public function getMessages(): Messages;
}
