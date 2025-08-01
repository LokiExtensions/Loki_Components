<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Loki\Components\Filter\Filter;
use Loki\Components\Messages\LocalMessage;
use Loki\Components\Validator\Validator;

class ComponentViewModel implements ComponentViewModelInterface
{
    protected ?ComponentInterface $component = null;
    protected ?Validator $validator = null;
    protected ?Filter $filter = null;
    protected ?AbstractBlock $block = null;
    protected bool $lazyLoad = true;
    protected bool $allowRendering = true;

    public function setComponent(ComponentInterface $component): void
    {
        $this->component = $component;
    }

    public function setValidator(Validator $validator): void
    {
        $this->validator = $validator;
    }

    public function setFilter(Filter $filter): void
    {
        $this->filter = $filter;
    }

    public function setBlock(AbstractBlock $block): void
    {
        $this->block = $block;
    }

    public function getComponentName(): string
    {
        return $this->component->getName();
    }

    public function getElementId(): string
    {
        return preg_replace('/([^a-zA-Z0-9\-]+)/', '-', $this->block->getNameInLayout());
    }

    public function getValue(): mixed
    {
        if (false === $this->getComponent()->hasRepository()) {
            return null;
        }

        $value = $this->getComponent()->getRepository()->getValue();
        $value = $this->filter->filter($this->getComponent()->getFilters(), $value);
        $this->validator->validate($this->component, $value);

        return $value;
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function getValidators(): array
    {
        return [];
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getTargets(): array
    {
        return $this->getComponent()->getTargets();
    }

    public function getTargetString(): string
    {
        return $this->getComponent()->getTargetString();
    }

    public function getMessages(): array
    {
        return $this->component->getLocalMessageRegistry()->getMessagesByComponent($this->component);
    }

    public function getContext(): ComponentContextInterface
    {
        return $this->component->getContext();
    }

    protected function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    protected function getRepository(): ?ComponentRepositoryInterface
    {
        return $this->component->getRepository();
    }

    public function getTemplate(): ?string
    {
        return null;
    }

    public function getJsComponentName(): ?string
    {
        return $this->getBlock()->getJsComponentName();
    }

    public function getJsComponentId(): string
    {
        $componentId = ucwords($this->getBlock()->getNameInLayout(), '_.-');

        return preg_replace('#[_.-]+#', '', $componentId);
    }

    public function isValid(): bool
    {
        foreach ($this->getMessages() as $message) {
            if ($message->getType() === LocalMessage::TYPE_ERROR) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isLazyLoad(): bool
    {
        return $this->lazyLoad;
    }

    public function isAllowRendering(): bool
    {
        return $this->allowRendering;
    }

    public function getJsData(): array
    {
        return [
            ...(array)$this->getBlock()->getJsData(),
            'value' => $this->getValue(),
            'messages' => $this->getMessages(),
            'valid' => $this->isValid(),
        ];
    }
}
