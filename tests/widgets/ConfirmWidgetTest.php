<?php

declare(strict_types=1);

namespace tests\widgets;

use app\widgets\confirm\Confirm;
use tests\AppTestCase;

/**
 * Class ConfirmWidgetTest
 * @package tests\widgets
 */
class ConfirmWidgetTest extends AppTestCase
{
    public static $html = '<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h4 id="confirmationModalLabel" class="modal-title">Confirmation required</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" id="confirmationCancel" class="btn btn-outline-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button><button type="button" id="confirmationOk" class="btn btn-success" data-dismiss="modal" data-pjax="0"><i class="fas fa-check-circle"></i> Confirm</button></div></div></div></div>';

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testWithoutModal(): void
    {
        self::assertEmpty(Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testJustQuestion(): void
    {
        $out = Confirm::ask('justQuestion', true, true);

        self::assertSame('justQuestion', $out);

        self::assertEquals(static::$html, Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testNoPost(): void
    {
        $out = Confirm::ask('noPostQuestion', false);

        self::assertSame('data-confirm="noPostQuestion"', $out);

        self::assertEquals(static::$html, Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testDefault(): void
    {
        $out = Confirm::ask('defaultQuestion');

        self::assertSame('data-confirm="defaultQuestion" data-method="post"', $out);

        self::assertEquals(static::$html, Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testMultiple(): void
    {
        Confirm::ask('firstQuestion');
        Confirm::ask('secondQuestion');

        self::assertEquals(static::$html, Confirm::widget());
    }
}
