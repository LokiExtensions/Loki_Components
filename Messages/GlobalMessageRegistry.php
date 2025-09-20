<?php
declare(strict_types=1);

namespace Loki\Components\Messages;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * @deprecated Use \Magento\Framework\Message\ManagerInterface directly
 */
class GlobalMessageRegistry implements ArgumentInterface
{
    /** @var GlobalMessage[] */
    protected array $messages = [];

    public function __construct(
        private readonly MessageManager $messageManager,
    ) {
    }

    /**
     * @param string $message
     * @return void
     * @deprecated Use \Magento\Framework\Message\ManagerInterface directly
     */
    public function addSuccess(string $message): void
    {
        $this->messageManager->addSuccessMessage($message);
    }

    /**
     * @param string $message
     * @return void
     * @deprecated Use \Magento\Framework\Message\ManagerInterface directly
     */
    public function addNotice(string $message): void
    {
        $this->messageManager->addNoticeMessage($message);
    }

    /**
     * @param string $message
     * @return void
     * @deprecated Use \Magento\Framework\Message\ManagerInterface directly
     */
    public function addWarning(string $message): void
    {
        $this->messageManager->addWarningMessage($message);
    }

    /**
     * @param string $message
     * @return void
     * @deprecated Use \Magento\Framework\Message\ManagerInterface directly
     */
    public function addError(string $message): void
    {
        $this->messageManager->addErrorMessage($message);
    }
}
