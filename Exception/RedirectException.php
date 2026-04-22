<?php declare(strict_types=1);

namespace Loki\Components\Exception;

use RuntimeException;

class RedirectException extends RuntimeException
{
    private string $url;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): RedirectException
    {
        $this->url = $url;
        return $this;
    }
}
