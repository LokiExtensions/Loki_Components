<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Loki\Components\Attribute\JsProperty;

trait JsPropertyTraitStub
{
    #[JsProperty(name: 'fromTrait')]
    public function getFromTrait(): string
    {
        return 'trait';
    }
}
