<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Layout\Reader as PageLayoutReader;

class LayoutLoader
{
    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly LayoutHandlerComposite $layoutHandlerComposite,
        private readonly BuilderFactory $layoutBuilderFactory,
        private readonly GeneratorPool $generatorPool,
        private readonly RequestInterface $request,
        private readonly PageConfig $pageConfig,
        private readonly PageLayoutReader $pageLayoutReader,
    ) {
    }

    public function load(array $handles = [], bool $initializeElements = true): LayoutInterface
    {
        $handles = $this->layoutHandlerComposite->getHandles($handles);

        $layout = $this->layout;
        $layout->setGeneratorPool($this->generatorPool);

        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->addHandle(strtolower($this->request->getFullActionName()));

        foreach ($handles as $handle) {
            $update->addHandle($this->sanitizeHandle($handle));
        }

        $builder = $this->layoutBuilderFactory->create(
            BuilderFactory::TYPE_PAGE,
            [
                'layout' => $layout,
                'pageConfig' => $this->pageConfig,
                'pageLayoutReader' => $this->pageLayoutReader,
            ]
        );

        $builder->build();

        return $layout;
    }

    private function sanitizeHandle(string $handle): string
    {
        return preg_replace('/([^a-z0-9\-\_]+)/', '', $handle);
    }
}
