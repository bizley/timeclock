<?php

declare(strict_types=1);

namespace tests\widgets;

use app\widgets\modal\Clock;
use tests\AppTestCase;
use Yii;

/**
 * Class ClockModalWidgetTest
 * @package tests\widgets
 */
class ClockModalWidgetTest extends AppTestCase
{
    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testClockButton(): void
    {
        $view = Yii::$app->view;
        $out = Clock::button();

        $this->assertEquals('<span class="btn btn-success btn-lg btn-block clock" data-toggle="modal" data-target="#clockModal"><i class="glyphicon glyphicon-play"></i>Start Session</span>', $out);

        $this->assertTrue($view->params[Clock::CLOCK_MODAL]);
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     */
    public function testClockModal(): void
    {
        $out = Clock::widget(['params' => [Clock::CLOCK_MODAL => true]]);

        $this->assertContains(<<<HTML
<div id="clockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="clockModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="/index.php?r=clock%2Fstart" method="post">
HTML
            , $out);

        $this->assertContains(<<<HTML
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="clockModalLabel" class="modal-title">Confirmation required</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        Are you sure you want to start session?                    </div>
                    <textarea class="form-control" name="note" style="resize:vertical" placeholder="Optional Session Note"></textarea>                </div>
                <div class="modal-footer">
                    <button type="button" id="clockCancel" class="btn btn-outline pull-left" data-dismiss="modal">
                        <i class="glyphicon glyphicon-ban-circle text-muted"></i> Cancel                    </button>
                    <button type="submit" id="clockOk" class="btn btn-success" data-pjax="0"><i class="glyphicon glyphicon-ok-circle"></i> Confirm</button>                </div>
            </form>        </div>
    </div>
</div>
HTML
            , $out);
    }
}
