<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Loki\Components\Attribute\JsProperty;

class JsPropertyChildViewModelStub extends JsPropertyParentViewModelStub
{
    public function getStep(): string
    {
        return 'child';
    }

    #[JsProperty(name: 'label')]
    public function getLabel(): string
    {
        return 'child-label';
    }

    public function getFromTrait(): string
    {
        return 'child-trait';
    }

    public function getSecret(): string
    {
        return 'child-secret';
    }

    #[JsProperty(name: 'extra')]
    public function getExtra(): string
    {
        return 'extra';
    }
}
