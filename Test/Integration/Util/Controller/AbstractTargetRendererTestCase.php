<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Controller;

use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Components\Util\Controller\TargetRenderer;
use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;

abstract class AbstractTargetRendererTestCase extends TestCase
{
    protected function getLayoutLoader(): LayoutLoader
    {
        return ObjectManager::getInstance()->get(LayoutLoader::class);
    }

    protected function getTargetRenderer(): TargetRenderer
    {
        return ObjectManager::getInstance()->get(TargetRenderer::class);
    }
}
