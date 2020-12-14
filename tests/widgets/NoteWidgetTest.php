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
        self::assertEmpty(Note::widget());
    }

    /**
     * @runInSeparateProcess
     */
    public function testWithNote(): void
    {
        $out = Note::widget(['model' => new Off(['note' => 'testNote'])]);

        self::assertEquals('<div class="note" title="Note" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="testNote"><i class="fas fa-sticky-note fa-spin"></i></div>', $out);
    }
}
