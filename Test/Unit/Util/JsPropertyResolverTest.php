<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Test\Unit\Stub\JsPropertyChildViewModelStub;
use Loki\Components\Test\Unit\Stub\JsPropertyParentViewModelStub;
use Loki\Components\Util\JsPropertyResolver;
use PHPUnit\Framework\TestCase;

class JsPropertyResolverTest extends TestCase
{
    public function testGetJsPropertiesFromDeclaredMethods(): void
    {
        $jsProperties = (new JsPropertyResolver())->getJsProperties(new JsPropertyParentViewModelStub());
        ksort($jsProperties);

        $this->assertSame([
            'fromTrait' => 'trait',
            'label' => 'parent-label',
            'secret' => 'parent-secret',
            'step' => 'parent',
            'value' => 'parent-value',
        ], $jsProperties);
    }

    public function testGetJsPropertiesInheritsAttributesOfOverriddenMethods(): void
    {
        $jsProperties = (new JsPropertyResolver())->getJsProperties(new JsPropertyChildViewModelStub());
        ksort($jsProperties);

        $this->assertSame([
            'extra' => 'extra',
            'fromTrait' => 'child-trait',
            'label' => 'child-label',
            'step' => 'child',
            'value' => 'parent-value',
        ], $jsProperties);
    }
}
