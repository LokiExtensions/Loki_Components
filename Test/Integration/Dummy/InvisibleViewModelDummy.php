<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Dummy;

use Loki\Components\Component\ComponentViewModel;

class InvisibleViewModelDummy extends ComponentViewModel
{
    protected bool $visible = false;
}
