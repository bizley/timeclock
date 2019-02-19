<?php

use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $session \app\models\Clock
 * @var $clock array
 * @var $employee \app\models\User
 * @var $user \app\models\User
 * @var $users array
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

$this->title = Yii::t('app', 'Sessions');

$total = [];
$list = '';
$sessions = [];

foreach ($clock as $session) {
    $sessions[Yii::$app->formatter->asDatetime($session->clock_in, 'd')][] = $session;

    if (!array_key_exists($session->user_id, $total)) {
        $total[$session->user_id] = 0;
    }

    if ($session->clock_out !== null) {
        $total[$session->user_id] += $session->clock_out - $session->clock_in;
    }
}

$buttonTexts = [
    'show' => Yii::t('app', 'show details'),
    'hide' => Yii::t('app', 'hide details'),
];

foreach ($sessions as $day => $sessionsInDay) {
    if (count($sessionsInDay) === 1) {
        $list .= $this->render('history-row', [
            'session' => $sessionsInDay[0],
            'day' => null,
            'users' => $users,
        ]);
    } else {
        $daySessions = '';
        $dayTime = 0;

        foreach ($sessionsInDay as $session) {
            $daySessions .= $this->render('history-row', [
                'session' => $session,
                'day' => $day,
                'users' => $users,
            ]);

            if ($session->clock_out !== null) {
                $dayTime += $session->clock_out - $session->clock_in;
            }
        }

        $list .= Html::beginTag('li', ['class' => 'list-group-item']);

        if ($dayTime) {
            $list .= Html::tag(
                'span',
                Yii::$app->formatter->asDuration($dayTime),
                ['class' => 'badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3']
            );
        }

        $list .= Html::a(
            FA::icon('angle-double-down') . ' ' . Html::tag('span', $buttonTexts['show'], ['class' => 'd-none d-md-inline']),
            '#',
            [
                'class' => 'btn btn-outline-secondary btn-sm float-left mr-1 sessionDetailsButton',
                'data-target' => '.day' . $day,
            ]
        );
        $list .= Yii::$app->formatter->asDate($sessionsInDay[0]->clock_in);
        $list .= ' ' . Html::tag(
            'span',
            Yii::t('app', '{n} sessions', ['n' => count($sessionsInDay)]),
            ['class' => 'badge badge-pill badge-primary']
        );
        $list .= Html::endTag('li');
        $list .= $daySessions;
    }
}

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
    <h1><?= Yii::t('app', 'Sessions') ?></h1>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['admin/history'], 'get'); ?>
            <?= Html::hiddenInput('id', $employee !== null ? $employee->id : null) ?>
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
                            ['history', 'month' => $previousMonth, 'year' => $previousYear, 'id' => $employee !== null ? $employee->id : null],
                            ['class' => 'btn btn-primary']
                        ) ?><?= Html::a(
                            FA::icon('step-forward') . $next,
                            ['history', 'month' => $nextMonth, 'year' => $nextYear, 'id' => $employee !== null ? $employee->id : null],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group">
            <?= Html::a(
            FA::icon('calendar-alt') . ' ' . Yii::t('app', 'Switch To Calendar'),
                ['calendar', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
        <div class="form-group mb-5">
            <div class="list-group">
                <?php foreach ($users as $user): ?>
                    <a href="<?= Url::to(['history', 'month' => $month, 'year' => $year, 'id' => $user->id]) ?>"
                       class="list-group-item <?= $employee !== null && $employee->id === $user->id ? 'active' : '' ?>">
                        <?= Html::encode($user->name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="form-group">
            <?php if ($employee !== null): ?>
                <a href="<?= Url::to(['history', 'month' => $month, 'year' => $year]) ?>" class="btn btn-success btn-sm float-right">
                    <?= FA::icon('users') ?> <?= Yii::t('app', 'All Employees') ?>
                </a>
                <?= Html::encode($employee->name) ?>
            <?php endif; ?>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <ul class="list-group mb-3">
            <li class="list-group-item">
                <span class="badge badge-light float-sm-right d-block d-sm-inline mb-1 ml-0 ml-sm-3">
                    <?= round(array_sum($total) / 3600, 2) ?>
                </span>
                <?= Yii::t('app', 'Total Hours') ?>
            </li>
            <?php if ($employee === null): ?>
                <?php foreach ($users as $user): ?>
                    <li class="list-group-item">
                        <span class="badge badge-light float-sm-right d-block d-sm-inline mb-1 ml-0 ml-sm-3">
                            <?= isset($total[$user->id]) ? round($total[$user->id] / 3600, 2) : 0 ?>
                        </span>
                        <?= Html::encode($user->name) ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <ul class="list-group mb-3"><?= $list ?></ul>
        <div class="form-group">
            <?= Yii::t('app', 'Off-Time') ?>
        </div>
        <ul class="list-group mb-3">
            <?php foreach ($off as $day): ?>
                <li class="list-group-item">
                    <?= Note::widget(['model' => $day]) ?>
                    <?= Html::encode($users[$day->user_id]->name) ?>
                    <?= Yii::$app->formatter->asDate($day->start_at) ?>
                    <?= FA::icon('long-arrow-alt-right') ?>
                    <?= Yii::$app->formatter->asDate($day->end_at) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
