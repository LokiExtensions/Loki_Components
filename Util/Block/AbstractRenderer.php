<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use RuntimeException;

abstract class AbstractRenderer implements ArgumentInterface
{
    protected ?AbstractBlock $ancestorBlock = null;

    public function __construct(
        protected LayoutInterface $layout,
        protected AppState $appState,
        protected ChildCounter $childCounter,
    ) {
    }

    protected function populateBlock(
        AbstractBlock $block,
        array $data = []
    ): void {
        $block->addData($data);
        $block->setAncestorBlock($this->ancestorBlock);
        $block->setUniqId($this->getUniqId($block, $data));

        $viewModel = $this->ancestorBlock->getViewModel();
        if ($viewModel instanceof ComponentViewModelInterface) {
            $block->setViewModel($viewModel);
        }
    }

    protected function getCounter(AbstractBlock $block): int
    {
        return $this->childCounter->getCounter($block->getNameInLayout());
    }

    protected function getBlockAlias(
        ?AbstractBlock $block = null,
        array $data = []
    ): string {
        if ($block instanceof AbstractBlock) {
            $alias = $block->getAlias();
            if (!empty($alias)) {
                return $alias;
            }
        }

        if (isset($data['alias'])) {
            return $data['alias'];
        }

        if ($this->ancestorBlock instanceof AbstractBlock) {
            return 'block' . $this->getCounter($this->ancestorBlock);
        }

        return '';
    }

    protected function setNameInLayout(AbstractBlock $block): void
    {
        $alias = $this->getBlockAlias($block);
        $block->setNameInLayout(
            $this->ancestorBlock->getNameInLayout() . '.' . $alias
        );
    }

    protected function getUniqId(AbstractBlock $block, array $data = []): string
    {
        $ancestorId = $this->ancestorBlock->getNameInLayout();

        if (isset($data['uniq'])) {
            return $ancestorId . '-' . $data['uniq'];
        }

        $blockParts = explode('.', $block->getNameInLayout());
        return $ancestorId . '-' . array_pop($blockParts);
    }

    protected function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER;
    }
}
