<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub\JsPropertyInterceptedViewModelStub;

use Loki\Components\Test\Unit\Stub\JsPropertyInterceptedViewModelStub;
use Magento\Framework\Interception\InterceptorInterface;
use Magento\Framework\Interception\PluginListInterface;

class Interceptor extends JsPropertyInterceptedViewModelStub implements InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(PluginListInterface $pluginList)
    {
        $this->pluginList = $pluginList;
        $this->subjectType = get_parent_class($this);
    }

    public function getValue(): mixed
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getValue');
        return $pluginInfo ? $this->___callPlugins('getValue', func_get_args(), $pluginInfo) : parent::getValue();
    }

    public function getStep(): string
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStep');
        return $pluginInfo ? $this->___callPlugins('getStep', func_get_args(), $pluginInfo) : parent::getStep();
    }

    public function getLabel(): string
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLabel');
        return $pluginInfo ? $this->___callPlugins('getLabel', func_get_args(), $pluginInfo) : parent::getLabel();
    }

    public function getTitle(): string
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTitle');
        return $pluginInfo ? $this->___callPlugins('getTitle', func_get_args(), $pluginInfo) : parent::getTitle();
    }

    public function getFromTrait(): string
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFromTrait');
        return $pluginInfo
            ? $this->___callPlugins('getFromTrait', func_get_args(), $pluginInfo)
            : parent::getFromTrait();
    }
}
