<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

interface ComponentInterface
{
    public function getContext(): ComponentContextInterface;

    public function getViewModel(): ?ComponentViewModelInterface;

    public function getRepository(): ?ComponentRepositoryInterface;

    public function getSources(): array;

    public function getTargets(): array;

    public function getTargetString(): string;

    public function getValidators(): array;

    public function getFilters(): array;
}
