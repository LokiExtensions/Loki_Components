<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Controller;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Util\Controller\AllowedTargets;
use Loki\Components\Util\Controller\ComponentUpdate;
use Loki\Components\Util\DefaultTargets;
use Loki\Components\Util\IdConvertor;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class AllowedTargetsTest extends TestCase
{
    public function testKeepsTargetsOfTheUpdatedComponents(): void
    {
        $targets = [
            'loki.checkout.shipping',
            'loki-checkout-billing',
            'loki-checkout-block-target',
            'loki-checkout-view-model-target',
            'loki-checkout-self',
            'loki.script.message-transfer',
        ];

        $this->assertSame($targets, $this->getAllowedTargets()->filter($targets, [$this->getComponentUpdate()]));
    }

    public function testDropsUndeclaredTargets(): void
    {
        $targets = ['customer_account_dashboard_info', 'loki.checkout.shipping', 'customer-account-navigation', 42, null];

        $this->assertSame(
            ['loki.checkout.shipping'],
            $this->getAllowedTargets()->filter($targets, [$this->getComponentUpdate()])
        );
    }

    public function testOnlyAllowsDefaultTargetsWithoutUpdates(): void
    {
        $this->assertSame(
            ['loki-script-message-transfer'],
            $this->getAllowedTargets()->filter(['loki.checkout.shipping', 'loki-script-message-transfer'], [])
        );
    }

    private function getAllowedTargets(): AllowedTargets
    {
        return new AllowedTargets(new IdConvertor(), new DefaultTargets(['loki.script.message-transfer']));
    }

    private function getComponentUpdate(): ComponentUpdate
    {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $viewModel->method('getTargets')->willReturn(['loki.checkout.view-model-target']);

        $component = $this->createMock(Component::class);
        $component->method('getName')->willReturn('loki.checkout.self');
        $component->method('getTargets')->willReturn(['loki.checkout.shipping', 'loki.checkout.billing']);
        $component->method('getViewModel')->willReturn($viewModel);

        $block = $this->createMock(Template::class);
        $block->method('getNameInLayout')->willReturn('loki.checkout.self');
        $block->method('__call')->willReturnCallback(fn (string $method) => match ($method) {
            'getTargets' => ['loki.checkout.block-target'],
            default => null,
        });

        return new ComponentUpdate($block, $component);
    }
}
