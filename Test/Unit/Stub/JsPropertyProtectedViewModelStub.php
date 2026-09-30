<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Loki\Components\Attribute\JsProperty;
use Loki\Components\Component\ComponentViewModel;

class JsPropertyProtectedViewModelStub extends ComponentViewModel
{
    public function getValue(): mixed
    {
        return 'protected-value';
    }

    #[JsProperty(name: 'shielded')]
    protected function getShielded(): string
    {
        return 'shielded';
    }
}
