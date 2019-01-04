<?php

declare(strict_types=1);

namespace tests\widgets;

use app\models\Off;
use app\widgets\note\Note;
use tests\DbTestCase;

/**
 * Class NoteWidgetTest
 * @package tests\widgets
 */
class NoteWidgetTest extends DbTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testWithoutNote(): void
    {
        $this->assertEmpty(Note::widget());
    }

    /**
     * @runInSeparateProcess
     */
    public function testWithNote(): void
    {
        $out = Note::widget(['offtime' => new Off(['note' => 'testNote'])]);

        $this->assertEquals(
            '<span class="text-info pull-right text-danger" title="Off-time Note" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="testNote"><i class="glyphicon glyphicon-comment"></i></span>',
            $out
        );
    }
}
