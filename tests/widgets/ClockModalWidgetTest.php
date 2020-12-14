<?php

declare(strict_types=1);

namespace tests\widgets;

use app\widgets\modal\Clock;
use Exception;
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
     * @throws Exception
     */
    public function testClockButton(): void
    {
        $view = Yii::$app->view;
        $out = Clock::button();

        self::assertEquals('<span class="btn btn-success btn-lg btn-block clock" data-toggle="modal" data-target="#clockModal"><i class="fas fa-play"></i>Start Session</span>', $out);

        self::assertTrue($view->params[Clock::CLOCK_MODAL]);
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testClockModal(): void
    {
        $out = Clock::widget(['params' => [Clock::CLOCK_MODAL => true]]);

        self::assertStringContainsString(<<<HTML
<div id="clockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="clockModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/index.php?r=clock%2Fstart" method="post">
HTML
            , $out);

        self::assertStringContainsString(<<<HTML
<div class="modal-header">
                    <h4 id="clockModalLabel" class="modal-title">Confirmation required</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Are you sure you want to start session?</strong>
                    </div>
                    <div class="form-group">
                        <label for="clock-project">Select project assigned to this session:</label>                        <select id="clock-project" class="custom-select" name="project_id">
<option value="">-- no project --</option>
</select>                    </div>
                    <textarea class="form-control" name="note" placeholder="Optional Session Note"></textarea>                </div>
                <div class="modal-footer">
                    <button type="button" id="clockCancel" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel                    </button>
                    <button type="submit" id="clockOk" class="btn btn-success" data-pjax="0"><i class="fas fa-check-circle"></i> Confirm</button>                </div>
            </form>        </div>
    </div>
</div>
HTML
            , $out);
    }
}
