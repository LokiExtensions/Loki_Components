<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Layout\Reader as PageLayoutReader;

class LayoutLoader
{
    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly LayoutFactory $layoutFactory,
        private readonly LayoutHandlerComposite $layoutHandlerComposite,
        private readonly BuilderFactory $layoutBuilderFactory,
        private readonly GeneratorPool $generatorPool,
        private readonly RequestInterface $request,
        private readonly PageConfig $pageConfig,
        private readonly PageLayoutReader $pageLayoutReader,
    ) {
    }

    public function load(array $handles = [], bool $isolated = false): LayoutInterface
    {
        $handles = $this->layoutHandlerComposite->getHandles($handles);

        /** @var Layout $layout */
        $layout = ($isolated) ? $this->layoutFactory->create() : $this->layout;
        $layout->setGeneratorPool($this->generatorPool);

        $update = $layout->getUpdate();
        $update->addHandle('default');

        /** @var Http $request */
        $request = $this->request;
        $update->addHandle(strtolower($request->getFullActionName()));

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
