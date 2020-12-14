<?php

declare(strict_types=1);

namespace tests\widgets;

use app\widgets\modal\Day;
use Exception;
use tests\AppTestCase;
use Yii;
use yii\web\View;

/**
 * Class DayModalWidgetTest
 * @package tests\widgets
 */
class DayModalWidgetTest extends AppTestCase
{
    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testDayModal(): void
    {
        $out = Day::widget(['params' => [Day::DAY_MODAL => true]]);

        self::assertEquals('<div id="dayModal" class="modal fade" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg modal-dialog-centered" role="document"><div class="modal-content modal-day"></div></div></div>', $out);
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testNoModal(): void
    {
        self::assertEmpty(Day::widget());
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testDayAdd(): void
    {
        $view = Yii::$app->view;
        $out = Day::add('PB', 12, 12, 2019, 1);

        self::assertEquals('<a class="badge badge-primary day" href="/index.php?r=admin%2Fday&amp;day=12&amp;month=12&amp;year=2019&amp;employee=1">PB</a>', $out);

        self::assertContains(<<<JS
$(".day").click(function (e) {
    e.preventDefault();
    let url = $(this).attr("href");
    $("#dayModal .modal-content").load(url, function () {
        $("#dayModal").modal("toggle");
    });
});
JS
            , $view->js[View::POS_READY]);

        self::assertTrue($view->params[Day::DAY_MODAL]);
    }
}
