<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Loki\Components\Layout\LayoutHandlerComposite;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface as PageLayoutConfigBuilder;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\ConfigFactory as PageConfigFactory;
use Magento\Framework\View\Page\Layout\Reader as PageLayoutReader;

class LayoutLoader
{
    public function __construct(
        private readonly LayoutInterface $layout,
        private readonly LayoutFactory $layoutFactory,
        private readonly LayoutHandlerComposite $layoutHandlerComposite,
        private readonly BuilderFactory $layoutBuilderFactory,
        private readonly PageConfig $pageConfig,
        private readonly PageConfigFactory $pageConfigFactory,
        private readonly PageLayoutReader $pageLayoutReader,
        private readonly PageLayoutConfigBuilder $pageLayoutConfigBuilder,
    ) {
    }

    public function load(array $handles = [], array $pageHandles = [], bool $isolated = false): LayoutInterface
    {
        if ($isolated) {
            $handles = $this->layoutHandlerComposite->getHandles($handles);
            $layout = $this->layoutFactory->create();
            $update = $layout->getUpdate();

            $this->applyPageHandles($update, $pageHandles);

            $update->addHandle('default');
            foreach ($handles as $handle) {
                $update->addHandle($this->sanitizeHandle($handle));
            }

            $pageConfig = $this->pageConfigFactory->create(); // important: fresh instance
            $this->applyPageLayout($pageConfig, $pageHandles);

            $builder = $this->layoutBuilderFactory->create(
                BuilderFactory::TYPE_PAGE,
                [
                    'layout' => $layout,
                    'pageConfig' => $pageConfig,
                    'pageLayoutReader' => $this->pageLayoutReader,
                ]
            );

            $builder->build();

            return $layout;
        }

        $handles = $this->layoutHandlerComposite->getHandles($handles);
        $update = $this->layout->getUpdate();

        $this->applyPageHandles($update, $pageHandles);

        $update->addHandle('default');
        foreach ($handles as $handle) {
            $update->addHandle($this->sanitizeHandle($handle));
        }

        $this->applyPageLayout($this->pageConfig, $pageHandles);

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

    private function applyPageHandles(ProcessorInterface $update, array $pageHandles): void
    {
        if (empty($pageHandles)) {
            return;
        }

        $update->addPageHandles($pageHandles);

        $pageLayoutsConfig = $this->pageLayoutConfigBuilder->getPageLayoutsConfig();
        foreach ($pageHandles as $pageHandle) {
            $sanitized = $this->sanitizeHandle($pageHandle);
            if ($pageLayoutsConfig->hasPageLayout($sanitized)) {
                $update->addHandle($sanitized);
            }
        }
    }

    private function applyPageLayout(PageConfig $pageConfig, array $pageHandles): void
    {
        if (empty($pageHandles)) {
            return;
        }

        $pageLayoutsConfig = $this->pageLayoutConfigBuilder->getPageLayoutsConfig();
        foreach ($pageHandles as $pageHandle) {
            $sanitized = $this->sanitizeHandle($pageHandle);
            if ($pageLayoutsConfig->hasPageLayout($sanitized)) {
                $pageConfig->setPageLayout($sanitized);
            }
        }
    }

    private function sanitizeHandle(string $handle): string
    {
        return preg_replace('/([^a-z0-9\-_]+)/', '', $handle);
    }
}
