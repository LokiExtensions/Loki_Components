<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

class JsPropertyPluginStub
{
    public function afterGetValue(object $subject, mixed $result): string
    {
        return $result . '-plugged';
    }

    public function afterGetStep(object $subject, string $result): string
    {
        return $result . '-plugged';
    }

    public function afterGetLabel(object $subject, string $result): string
    {
        return $result . '-plugged';
    }

    public function afterGetTitle(object $subject, string $result): string
    {
        return $result . '-plugged';
    }

    public function afterGetFromTrait(object $subject, string $result): string
    {
        return $result . '-plugged';
    }
}
