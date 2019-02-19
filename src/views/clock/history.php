<?php

use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $session \app\models\Clock
 * @var $clock array
 * @var $months array
 * @var $month int
 * @var $year int
 * @var $previousMonth int
 * @var $previousYear int
 * @var $nextMonth int
 * @var $nextYear int
 * @var $previous string
 * @var $next string
 * @var $day \app\models\Off
 * @var $off array
 */

$this->title = Yii::t('app', 'History');

$total = 0;
$sessions = [];

foreach ($clock as $session) {
    $sessions[Yii::$app->formatter->asDatetime($session->clock_in, 'd')][] = $session;

    if ($session->clock_out !== null) {
        $total += $session->clock_out - $session->clock_in;
    }
}

$buttonTexts = [
    'show' => Yii::t('app', 'show details'),
    'hide' => Yii::t('app', 'hide details'),
];

$this->registerJs(<<<JS
$(".sessionDetailsButton").click(function (e) {
    e.preventDefault();
    let details = $($(this).data("target"));
    let button = $(this);
    if ($(this).hasClass("detailsDisplayed")) {
        details.animate({"opacity": 0}, 100).hide("fast", function () {
            button.removeClass("detailsDisplayed");
            button.find("span").text("{$buttonTexts['show']}");
            button.find("i").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
        });
    } else {
        details.show("fast", function () {
            $(this).animate({"opacity": 1});
            button.addClass("detailsDisplayed");
            button.find("span").text("{$buttonTexts['hide']}");
            button.find("i").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
        });
    }
});
JS
);
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'History') ?></h1>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['clock/history'], 'get'); ?>
            <div class="form-group">
                <?= Html::dropDownList('month', $month, $months, ['class' => 'form-control custom-select']) ?>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::textInput('year', $year, ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::submitButton(FA::icon('play'), ['class' => 'btn btn-warning btn-block']) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group btn-group btn-block months" role="group">
                        <?= Html::a(
                            FA::icon('step-backward') . $previous,
                            ['history', 'month' => $previousMonth, 'year' => $previousYear],
                            ['class' => 'btn btn-primary']
                        ) ?><?= Html::a(
                            FA::icon('step-forward') . $next,
                            ['history', 'month' => $nextMonth, 'year' => $nextYear],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group mb-5">
            <?= Html::a(
            FA::icon('calendar-alt') . ' ' . Yii::t('app', 'Switch To Calendar'),
                ['calendar', 'month' => $month, 'year' => $year],
                ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="form-group">
            <a href="<?= Url::to(['clock/add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-success btn-sm float-right">
                <?= FA::icon('plus') ?> <?= Yii::t('app', 'Add Session') ?>
            </a>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <ul class="list-group mb-3">
            <?php foreach ($sessions as $day => $sessionsInDay): ?>
                <?php if (count($sessionsInDay) === 1): ?>
                    <?= $this->render('history-row', [
                        'session' => $sessionsInDay[0],
                        'day' => null,
                    ]) ?>
                <?php else: ?>
                    <?php
                    $daySessions = '';
                    $dayTime = 0;
                    foreach ($sessionsInDay as $session) {
                        $daySessions .= $this->render('history-row', [
                            'session' => $session,
                            'day' => $day,
                        ]);
                        if ($session->clock_out !== null) {
                            $dayTime += $session->clock_out - $session->clock_in;
                        }
                    } ?>
                    <li class="list-group-item">
                        <?php if ($dayTime): ?>
                            <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                                <?= Yii::$app->formatter->asDuration($dayTime) ?>
                            </span>
                        <?php endif; ?>
                        <a href="#" class="btn btn-outline-secondary btn-sm float-left mr-1 sessionDetailsButton" data-target=".day<?= $day ?>">
                            <?= FA::icon('angle-double-down') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'show details') ?></span>
                        </a>
                        <?= Yii::$app->formatter->asDate($sessionsInDay[0]->clock_in) ?>
                        <span class="badge badge-pill badge-primary">
                            <?= Yii::t('app', '{n} sessions', ['n' => count($sessionsInDay)]) ?>
                        </span>
                    </li>
                    <?= $daySessions ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <ul class="list-group mb-3">
            <li class="list-group-item">
                <span class="badge badge-light float-sm-right d-block d-sm-inline mb-1 ml-0 ml-sm-3">
                    <?= round($total / 3600, 2) ?> (<?= Yii::$app->formatter->asDuration($total) ?>)
                </span>
                <?= Yii::t('app', 'Total Hours') ?>
            </li>
        </ul>
        <div class="form-group">
            <a href="<?= Url::to(['clock/off-add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-warning btn-sm float-right">
                <?= FA::icon('plus-circle') ?> <?= Yii::t('app', 'Add Off-Time') ?>
            </a>
            <?= Yii::t('app', 'Off-Time') ?>
        </div>
        <?php if ($off): ?>
            <ul class="list-group">
                <?php foreach ($off as $day): ?>
                    <li class="list-group-item">
                        <a href="<?= Url::to(['clock/off-delete', 'id' => $day->id]) ?>"
                           class="btn btn-outline-danger btn-sm"
                            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this off-time?')) ?>>
                            <?= FA::icon('times') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'delete') ?></span>
                        </a>
                        <a href="<?= Url::to(['clock/off-edit', 'id' => $day->id]) ?>" class="btn btn-outline-warning btn-sm float-left mr-1">
                            <?= FA::icon('clock') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'edit') ?></span>
                        </a>
                        <?= Yii::$app->formatter->asDate($day->start_at) ?>
                        <?= FA::icon('long-arrow-alt-right') ?>
                        <?= Yii::$app->formatter->asDate($day->end_at) ?>
                        <?= Note::widget(['model' => $day]) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="form-group"><?= Yii::t('app', 'NONE') ?></div>
        <?php endif; ?>
    </div>
</div>
