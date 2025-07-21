<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;

class LayoutLoaderTest extends TestCase
{
    public function testLoad()
    {
        $layoutLoader = ObjectManager::getInstance()->get(LayoutLoader::class);
        $layout = $layoutLoader->load(['foobar', 'foo_$_bar']);

        $handles = $layout->getUpdate()->getHandles();
        $this->assertNotEmpty($handles);
        $this->assertContains('foobar', $handles);
        $this->assertContains('foo__bar', $handles);
    }
}
