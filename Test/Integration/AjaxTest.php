<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Test\Integration\Util;

use Laminas\Http\Headers;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use PHPUnit\Framework\TestCase;
use Yireo\LokiComponents\Util\Ajax;

class AjaxTest extends TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testIsAjax(array $headerArray, bool $expectedResult)
    {
        $headers = new Headers();
        $headers->addHeaders($headerArray);

        $httpRequest = ObjectManager::getInstance()->create(HttpRequest::class);
        $httpRequest->setHeaders($headers);

        $ajax = ObjectManager::getInstance()->create(Ajax::class, ['httpRequest' => $httpRequest]);
        $this->assertEquals($expectedResult, $ajax->isAjax(), var_export($headerArray, true));
    }

    public function getTestData(): array
    {
        return [
            [['X-Alpine-Request' => 'true'], true],
            [['X-Alpine-Request' => 1], true],
            [['X-Alpine-Request' => 0], false],
        ];
    }
}
