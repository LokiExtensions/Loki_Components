<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\GlobalMessages;

use Yireo\LokiComponents\Component\ComponentContextInterface;
use Yireo\LokiComponents\Config\Config;

class GlobalMessagesContext implements ComponentContextInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
