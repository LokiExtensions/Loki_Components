<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Loki\Components\Attribute\JsProperty;
use Loki\Components\Component\ComponentViewModel;

class JsPropertyParentViewModelStub extends ComponentViewModel
{
    use JsPropertyTraitStub;

    public function getValue(): mixed
    {
        return 'parent-value';
    }

    #[JsProperty(name: 'step')]
    public function getStep(): string
    {
        return 'parent';
    }

    #[JsProperty(name: 'label')]
    public function getLabel(): string
    {
        return 'parent-label';
    }
}
