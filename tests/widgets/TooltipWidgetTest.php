<?php

declare(strict_types=1);

namespace tests\widgets;

use app\widgets\tooltip\Tooltip;
use tests\DbTestCase;

/**
 * Class TooltipWidgetTest
 * @package tests\widgets
 */
class TooltipWidgetTest extends DbTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testWithoutTitle(): void
    {
        $this->assertEmpty(Tooltip::add());
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefault(): void
    {
        $this->assertEquals('data-toggle="tooltip" data-placement="top" title="tooltip"', Tooltip::add('tooltip'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testPosition(): void
    {
        $this->assertEquals('data-toggle="tooltip" data-placement="position" title="message"', Tooltip::add('message', 'position'));
    }
}
