<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Mock;

use Magento\Framework\View\Element\Template;

trait BlockMock
{
    protected function getBlockMock(string $nameInLayout = '', array $data = []): Template
    {
        $block = $this->getMockBuilder(Template::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getNameInLayout'])
            ->getMock();

        $block->method('getNameInLayout')->willReturn($nameInLayout);
        $block->setData($data);

        return $block;
    }
}
