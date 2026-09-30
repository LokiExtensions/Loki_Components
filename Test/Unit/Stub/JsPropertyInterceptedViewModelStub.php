<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Loki\Components\Attribute\JsProperty;

class JsPropertyInterceptedViewModelStub extends JsPropertyParentViewModelStub
{
    public function getStep(): string
    {
        return 'concrete-step';
    }

    #[JsProperty(name: 'title')]
    public function getTitle(): string
    {
        return 'concrete-title';
    }
}
