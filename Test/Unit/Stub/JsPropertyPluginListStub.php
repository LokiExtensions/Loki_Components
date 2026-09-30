<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Stub;

use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\PluginListInterface;

class JsPropertyPluginListStub implements PluginListInterface
{
    private const PLUGIN_CODE = 'jsPropertyPlugin';

    private JsPropertyPluginStub $plugin;

    public function __construct()
    {
        $this->plugin = new JsPropertyPluginStub();
    }

    public function getPlugin($type, $code)
    {
        return $this->plugin;
    }

    public function getNext($type, $method, $code = '__self')
    {
        if ($code !== '__self' || false === method_exists($this->plugin, 'after' . ucfirst($method))) {
            return null;
        }

        return [DefinitionInterface::LISTENER_AFTER => [self::PLUGIN_CODE]];
    }
}
