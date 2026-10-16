<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Filter;

use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Loki\Components\Filter\Security;

class SecurityTest extends TestCase
{
    #[DataProvider('filterDataProvider')]
    public function testFilter(string $input, string $expected): void
    {
        $filterScope = new FilterScope();
        $filter = new Security();
        $this->assertSame($expected, $filter->filter($input, $filterScope));
    }

    public static function filterDataProvider(): array
    {
        return [
            'script tag with content' => ['<script>foobar</script>', 'foobar'],
            'bare script tag' => ['<script>', ''],
            'encoded script tag' => ['&lt;script&gt;', ''],
            'double encoded script tag' => ['&amp;lt;script&amp;gt;', ''],
            'broken out img onerror' => ['"><img src=x onerror=1>', '">'],
            'javascript scheme' => ['javascript:alert(1)', 'alert(1)'],
            'vbscript scheme' => ['vbscript:msgbox(1)', 'msgbox(1)'],
            'img onerror' => ['<img src=x onerror=alert(1)>', ''],
            'encoded bold tags' => ['&lt;b&gt;already&lt;/b&gt;', 'already'],
            'anchor with javascript href' => ['<a href="javascript:alert(1)">x</a>', 'x'],
            'benign markup with entity' => ['<b>bold</b> &amp; text', 'bold & text'],
            'plain text' => ['plain text', 'plain text'],
            'scheme with leading whitespace' => [' javascript:alert(1)', 'alert(1)'],
            'data scheme' => ['data:text/html;base64,PHNjcmlwdD4=', 'text/html;base64,PHNjcmlwdD4='],
            'word ending with data followed by colon' => ['Metadata: 123', 'Metadata: 123'],
            'word data followed by colon' => ['Check data: none', 'Check data: none'],
            'less than sign with space' => ['a < b', 'a < b'],
            'unclosed tag' => ['x<img src=x onerror=alert(1)', 'x'],
            'null byte cannot smuggle tags' => ["\0<script>", ''],
            'placeholder cannot smuggle tags' => ["\u{E000}script>alert(1)", 'script>alert(1)'],
            'less than sign followed by number' => ['5 <10 items', '5 <10 items'],
        ];
    }
}
