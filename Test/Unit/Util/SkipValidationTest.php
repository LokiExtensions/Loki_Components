<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util;

use Loki\Components\Util\SkipValidation;
use PHPUnit\Framework\TestCase;

class SkipValidationTest extends TestCase
{
    public function testSkipValidation(): void
    {
        $skipValidation = new SkipValidation();
        $this->assertFalse($skipValidation->isSkipValidation());

        $skipValidation->setSkipValidation(true);
        $this->assertTrue($skipValidation->isSkipValidation());

        $skipValidation->setSkipValidation(false);
        $this->assertFalse($skipValidation->isSkipValidation());
    }
}
