<?php

use app\models\Clock;
use app\models\Off;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $session Clock
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
 * @var $firstDayInMonth int
 * @var $daysInMonth int
 * @var $holidays array
 * @var $off array
 * @var $dayOff Off
 */

$this->title = Yii::t('app', 'Calendar');

$clockDays = [];
foreach ($clock as $session) {
    $day = Yii::$app->formatter->asDate($session->clock_in, 'd');
    if (!array_key_exists($day, $clockDays)) {
        $clockDays[$day] = [Yii::$app->formatter->asTime($session->clock_in, 'HH:mm')];
    }
    $clockDays[$day][1] = $session->clock_out !== null ? Yii::$app->formatter->asTime($session->clock_out, 'HH:mm') : '-';
}

$offDays = [];
for ($day = 1; $day <= $daysInMonth; $day++) {
    $stamp = (int)Yii::$app->formatter->asTimestamp($year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day . ' 12:00:00');
    foreach ($off as $dayOff) {
        if ($stamp >= (int)Yii::$app->formatter->asTimestamp($dayOff->start_at . ' 12:00:00')
            && $stamp <= (int)Yii::$app->formatter->asTimestamp($dayOff->end_at . ' 12:00:00')) {
            $offDays[] = $day;
            break;
        }
    }
}

$clockUrl = Url::to(['clock/add']);
$offUrl = Url::to(['clock/off-add']);

$this->registerJs(<<<JS
$(".selectDay")
    .click(function () {
        let calendar = $(this);
        window.location.href = "$clockUrl/" + calendar.data("day") + "/" + calendar.data("month") + "/" + calendar.data("year");
        return false;
    })
    .contextmenu(function () {
        let calendar = $(this);
        window.location.href = "$offUrl/" + calendar.data("day") + "/" + calendar.data("month") + "/" + calendar.data("year");
        return false;
    });
JS
);
?>
<div class="form-group mt-3">
    <h1><?= Yii::t('app', 'Calendar') ?></h1>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['clock/calendar'], 'get'); ?>
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
                            ['calendar', 'month' => $previousMonth, 'year' => $previousYear],
                            ['class' => 'btn btn-primary']
                        ) ?><?= Html::a(
                            FA::icon('step-forward') . $next,
                            ['calendar', 'month' => $nextMonth, 'year' => $nextYear],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group mb-3">
            <?= Html::a(
                FA::icon('history') . ' ' . Yii::t('app', 'Switch To History'),
                ['history', 'month' => $month, 'year' => $year],
                ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
        <div class="form-group mb-5">
            <?= Html::a(
                FA::icon('umbrella') . ' ' . Yii::t('app', 'Switch To Projects'),
                ['projects', 'month' => $month, 'year' => $year],
                ['class' => 'btn btn-light btn-block']
            ) ?>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="form-group">
            <div class="float-right">
                <a href="<?= Url::to(['clock/add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-success btn-sm">
                    <?= FA::icon('plus') ?> <?= Yii::t('app', 'Add Session') ?>
                </a>
                <a href="<?= Url::to(['clock/off-add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-warning btn-sm">
                    <?= FA::icon('plus-circle') ?> <?= Yii::t('app', 'Add Off-Time') ?>
                </a>
            </div>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <div class="form-group">
            <?php foreach (Clock::days() as $day): ?>
                <div class="calendar day"><?= $day ?></div>
            <?php endforeach; ?>
            <div class="clearfix"></div>
            <?php
            $dayOfWeek = $firstDayInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++): ?>
                <div class="calendar selectDay
                    <?= $dayOfWeek > 5 ? 'weekend' : '' ?>
                    <?= in_array($day, $holidays, true) ? 'holiday' : '' ?>
                    <?= in_array($day, $offDays, true) ? 'off' : '' ?>
                    <?= date('Y-m-d') === $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day ? 'today' : '' ?>"
                     style="<?= $day === 1 && $firstDayInMonth !== 1
                    ? 'margin-left:calc(' . (($firstDayInMonth - 1) * 0.5 + 0.25) . 'rem + ' . (($firstDayInMonth - 1) * 13) . '%'
                    : '' ?>" data-year="<?= $year ?>" data-month="<?= $month ?>" data-day="<?= $day ?>">
                    <span class="float-right d-block d-md-none"><?= Clock::days()[$dayOfWeek] ?></span>
                    <?= in_array($day, $offDays, true) ? Yii::t('app', 'off-time') : '' ?>
                    <?= $day ?>
                    <?php if (!array_key_exists($day, $clockDays)): ?>
                        <p>&nbsp;</p><p>&nbsp;</p>
                    <?php else: ?>
                        <p class="ml-3 ml-md-0"><small><?= FA::icon('play') ?></small> <?= $clockDays[$day][0] ?></p>
                        <p><small><?= FA::icon('stop') ?></small> <?= $clockDays[$day][1] ?></p>
                    <?php endif; ?>
                </div>
            <?php
            $dayOfWeek++;
            if ($dayOfWeek === 8): $dayOfWeek = 1; ?>
                <div class="clearfix"></div>
            <?php
            endif;
            endfor;
            ?>
        </div>
        <div class="form-group"><div class="clearfix"></div></div>
        <div class="form-group">
            <p class="text-muted small">
                <?= FA::icon('info-circle') ?> <?= Yii::t('app', 'Left-click day to add session. Right-click day to add off-time.') ?>
            </p>
        </div>
    </div>
</div>
