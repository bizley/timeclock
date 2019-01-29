<?php

declare(strict_types=1);

namespace app\widgets\modal;

use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

/**
 * Class Day
 * @package app\widgets\modal
 */
class Day extends Widget
{
    public const DAY_MODAL = 'dayModal';

    /**
     * @var array
     */
    public $params = [];

    /**
     * @param string $initials
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $userId
     * @return string
     */
    public static function add(string $initials, int $day, int $month, int $year, int $userId): string
    {
        $modal = new static();

        BootstrapPluginAsset::register($modal->view);

        $modal->view->registerJs(<<<JS
$(".day").click(function (e) {
    e.preventDefault();
    let url = $(this).attr("href");
    $("#dayModal .modal-content").load(url, function () {
        $("#dayModal").modal("toggle");
    });
});
JS
        );

        $modal->view->params[self::DAY_MODAL] = true;

        return Html::a(
            $initials,
            ['/admin/day', 'day' => $day, 'month' => $month, 'year' => $year, 'employee' => $userId],
            ['class' => 'btn btn-primary btn-xs day']
        );
    }

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        if (array_key_exists(self::DAY_MODAL, $this->params)) {
            return Html::tag(
                'div',
                Html::tag(
                    'div',
                    Html::tag(
                        'div',
                        '',
                        ['class' => 'modal-content modal-day']
                    ),
                    [
                        'class' => 'modal-dialog modal-lg',
                        'role' => 'document',
                    ]
                ),
                [
                    'class' => 'modal fade',
                    'id' => 'dayModal',
                    'tabindex' => '-1',
                    'role' => 'dialog',
                ]
            );
        }

        return null;
    }
}
