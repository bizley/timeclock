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
    public static $html = '<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 id="confirmationModalLabel" class="modal-title">Confirmation required</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" id="confirmationCancel" class="btn btn-outline pull-left" data-dismiss="modal"><i class="glyphicon glyphicon-ban-circle text-muted"></i> Cancel</button><button type="button" id="confirmationOk" class="btn btn-success" data-dismiss="modal" data-pjax="0"><i class="glyphicon glyphicon-ok-circle"></i> Confirm</button></div></div></div></div>';

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testWithoutModal(): void
    {
        $this->assertEmpty(Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testJustQuestion(): void
    {
        $out = Confirm::ask('justQuestion', true, true);

        $this->assertSame('justQuestion', $out);

        $this->assertEquals(static::$html, Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testNoPost(): void
    {
        $out = Confirm::ask('noPostQuestion', false);

        $this->assertSame('data-confirm="noPostQuestion"', $out);

        $this->assertEquals(static::$html, Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testDefault(): void
    {
        $out = Confirm::ask('defaultQuestion');

        $this->assertSame('data-confirm="defaultQuestion" data-method="post"', $out);

        $this->assertEquals(static::$html, Confirm::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testMultiple(): void
    {
        Confirm::ask('firstQuestion');
        Confirm::ask('secondQuestion');

        $this->assertEquals(static::$html, Confirm::widget());
    }
}
