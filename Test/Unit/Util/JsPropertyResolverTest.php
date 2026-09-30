<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Exception\InvalidJsPropertyException;
use Loki\Components\Test\Unit\Stub\JsPropertyChildViewModelStub;
use Loki\Components\Test\Unit\Stub\JsPropertyInterceptedViewModelStub;
use Loki\Components\Test\Unit\Stub\JsPropertyInterceptedViewModelStub\Interceptor;
use Loki\Components\Test\Unit\Stub\JsPropertyParentViewModelStub;
use Loki\Components\Test\Unit\Stub\JsPropertyPluginListStub;
use Loki\Components\Test\Unit\Stub\JsPropertyPrivateChildViewModelStub;
use Loki\Components\Test\Unit\Stub\JsPropertyPrivateViewModelStub;
use Loki\Components\Test\Unit\Stub\JsPropertyProtectedViewModelStub;
use Loki\Components\Util\JsPropertyResolver;
use Magento\Framework\Interception\InterceptorInterface;
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

    public function testGetJsPropertiesFromConcreteAndParentClassesWithoutPlugins(): void
    {
        $jsProperties = (new JsPropertyResolver())->getJsProperties(new JsPropertyInterceptedViewModelStub());
        ksort($jsProperties);

        $this->assertSame([
            'fromTrait' => 'trait',
            'label' => 'parent-label',
            'step' => 'concrete-step',
            'title' => 'concrete-title',
            'value' => 'parent-value',
        ], $jsProperties);
    }

    public function testGetJsPropertiesFromConcreteAndParentClassesWithPlugins(): void
    {
        $interceptor = new Interceptor(new JsPropertyPluginListStub());
        $this->assertInstanceOf(InterceptorInterface::class, $interceptor);

        $jsProperties = (new JsPropertyResolver())->getJsProperties($interceptor);
        ksort($jsProperties);

        $this->assertSame([
            'fromTrait' => 'trait-plugged',
            'label' => 'parent-label-plugged',
            'step' => 'concrete-step-plugged',
            'title' => 'concrete-title-plugged',
            'value' => 'parent-value-plugged',
        ], $jsProperties);
    }

    public function testThrowsExceptionForPrivateMethod(): void
    {
        $this->expectException(InvalidJsPropertyException::class);
        $this->expectExceptionMessage(JsPropertyPrivateViewModelStub::class . '::getSecret()');

        (new JsPropertyResolver())->getJsProperties(new JsPropertyPrivateViewModelStub());
    }

    public function testThrowsExceptionForProtectedMethod(): void
    {
        $this->expectException(InvalidJsPropertyException::class);
        $this->expectExceptionMessage(JsPropertyProtectedViewModelStub::class . '::getShielded()');

        (new JsPropertyResolver())->getJsProperties(new JsPropertyProtectedViewModelStub());
    }

    public function testThrowsExceptionForPrivateMethodInParentClass(): void
    {
        $this->expectException(InvalidJsPropertyException::class);
        $this->expectExceptionMessage(JsPropertyPrivateViewModelStub::class . '::getSecret()');

        (new JsPropertyResolver())->getJsProperties(new JsPropertyPrivateChildViewModelStub());
    }
}
