<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Controller;

use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Controller\Result\Raw;

class HtmlResult extends Raw
{
    protected function render(HttpResponseInterface $response)
    {
        $response->setHeader('Content-Type', 'text/html');

        return parent::render($response);
    }
}
