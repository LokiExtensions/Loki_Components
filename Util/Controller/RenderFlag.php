<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

class RenderFlag
{
    /**
     * Rendering is disabled when an update, or the value of an update, contains a render flag that is not 1
     */
    public function allowRendering(array $updates): bool
    {
        foreach ($updates as $update) {
            if (false === is_array($update)) {
                continue;
            }

            if (isset($update['render']) && false === $this->isEnabled($update['render'])) {
                return false;
            }

            $value = $update['update'] ?? null;
            if (is_array($value) && isset($value['render']) && false === $this->isEnabled($value['render'])) {
                return false;
            }
        }

        return true;
    }

    private function isEnabled(mixed $render): bool
    {
        return in_array($render, [1, '1', true], true);
    }
}
