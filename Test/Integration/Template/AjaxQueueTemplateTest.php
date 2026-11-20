<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Template;

use Loki\Components\Util\Controller\LayoutLoader;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AjaxQueueTemplateTest extends TestCase
{
    private const BLOCK_NAME = 'loki-components.script.ajax-queue';

    #[AppIsolation(true)]
    #[DataProvider('getMaliciousValues')]
    public function testRequestParamsCanNotBreakOutOfTheScript(string $value, string $forbidden): void
    {
        ObjectManager::getInstance()->get(RequestInterface::class)->setParams(['zz' => $value]);

        $html = $this->renderAjaxQueueBlock();

        $this->assertMatchesRegularExpression('/const componentData = \{.*\};/', $html);
        $this->assertStringNotContainsString('JSON.parse(\'', $html);
        $this->assertStringNotContainsString($forbidden, $html);
    }

    public static function getMaliciousValues(): array
    {
        return [
            'single quote' => ["'+alert(document.domain)+'", "'+alert(document.domain)+'"],
            'double quote' => ['"+alert(1)+"', '"+alert(1)+"'],
            'closing script' => ['</script><script>alert(1)</script>', '</script><script>alert(1)'],
            'ampersand' => ['&lt;b&gt;', '&lt;b&gt;'],
        ];
    }

    #[AppIsolation(true)]
    public function testFormKeyIsSentAsHeaderInsteadOfUrlParameter(): void
    {
        $html = $this->renderAjaxQueueBlock();

        $this->assertStringNotContainsString('&form_key=', $html);
        $this->assertStringNotContainsString('?form_key=', $html);
        $this->assertStringContainsString("'X-Magento-Form-Key': formKey", $html);
    }

    #[AppIsolation(true)]
    public function testInvalidUtf8InRequestParamsDoesNotBreakTheScript(): void
    {
        ObjectManager::getInstance()->get(RequestInterface::class)->setParams(['zz' => "foo\xFFbar", "k\xFE" => 'v']);

        $html = $this->renderAjaxQueueBlock();

        $this->assertMatchesRegularExpression('/const componentData = \{.*"zz":"foo\?bar".*\};/', $html);
        $this->assertMatchesRegularExpression('/signature: "[a-f0-9]{64}"/', $html);
    }

    private function renderAjaxQueueBlock(): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutLoader::class)->load(['loki_components']);
        $block = $layout->getBlock(self::BLOCK_NAME);
        $this->assertInstanceOf(AbstractBlock::class, $block);

        return (string)$block->toHtml();
    }
}
