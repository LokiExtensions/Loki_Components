<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Magento\Framework\View\Layout\BuilderFactory;
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
        private readonly PageConfig $pageConfig,
        private readonly PageLayoutReader $pageLayoutReader,
    ) {
    }

    public function load(array $handles = [], bool $isolated = false): LayoutInterface
    {
        if ($isolated) {
            $layout = $this->layoutFactory->create();
            $update = $layout->getUpdate();

            foreach ($handles as $handle) {
                $update->addHandle($this->sanitizeHandle($handle));
            }

            $layout->generateXml();
            $layout->generateElements();

            return $layout;
        }

        $handles = $this->layoutHandlerComposite->getHandles($handles);

        $update = $this->layout->getUpdate();
        foreach ($handles as $handle) {
            $update->addHandle($this->sanitizeHandle($handle));
        }

        $builder = $this->layoutBuilderFactory->create(
            BuilderFactory::TYPE_PAGE,
            [
                'layout' => $this->layout,
                'pageConfig' => $this->pageConfig,
                'pageLayoutReader' => $this->pageLayoutReader,
            ]
        );

        $builder->build();

        return $this->layout;
    }

    private function sanitizeHandle(string $handle): string
    {
        return preg_replace('/([^a-z0-9\-_]+)/', '', $handle);
    }
}
