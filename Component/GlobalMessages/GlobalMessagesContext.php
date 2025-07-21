<?php
declare(strict_types=1);

namespace Loki\Components\Component\GlobalMessages;

use Loki\Components\Component\ComponentContextInterface;
use Loki\Components\Config\Config;

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
