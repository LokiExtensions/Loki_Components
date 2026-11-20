<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Util\DefaultTargets;
use Loki\Components\Util\IdConvertor;

class AllowedTargets
{
    public function __construct(
        private readonly IdConvertor $idConvertor,
        private readonly DefaultTargets $defaultTargets,
    ) {
    }

    /**
     * Only keep targets that are declared by the components of the updates, so a request can not render random blocks
     *
     * @param ComponentUpdate[] $updates
     */
    public function filter(array $targets, array $updates): array
    {
        $allowedTargets = $this->getAllowedTargets($updates);

        $filteredTargets = array_filter($targets, function ($target) use ($allowedTargets) {
            return is_string($target) && isset($allowedTargets[$target]);
        });

        return array_values($filteredTargets);
    }

    /**
     * @param ComponentUpdate[] $updates
     */
    private function getAllowedTargets(array $updates): array
    {
        $targetNames = $this->defaultTargets->getTargets();
        foreach ($updates as $update) {
            $component = $update->getComponent();
            $block = $update->getBlock();

            $targetNames[] = $component->getName();
            $targetNames[] = $block->getNameInLayout();
            $targetNames = array_merge(
                $targetNames,
                $component->getTargets(),
                (array)$block->getTargets(),
                (array)$component->getViewModel()->getTargets()
            );
        }

        $allowedTargets = [];
        foreach ($targetNames as $targetName) {
            if (false === is_string($targetName) || $targetName === '') {
                continue;
            }

            $allowedTargets[$targetName] = true;
            $allowedTargets[$this->idConvertor->toElementId($targetName)] = true;
        }

        return $allowedTargets;
    }
}
