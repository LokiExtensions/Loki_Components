<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Loki\Components\Attribute\JsProperty;
use Loki\Components\Component\ComponentViewModel;

class JsPropertyPrivateViewModelStub extends ComponentViewModel
{
    public function getValue(): mixed
    {
        return 'private-value';
    }

    #[JsProperty(name: 'secret')]
    private function getSecret(): string
    {
        return 'secret';
    }
}
