<?php

use yii\bootstrap\Html;
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
 * @var $firstDayInMonth int
 * @var $daysInMonth int
 * @var $holidays array
 * @var $off array
 * @var $dayOff \app\models\Off
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
    $stamp = (new \DateTime(
        $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day . ' 12:00:00',
        new \DateTimeZone(Yii::$app->timeZone))
    )->getTimestamp();
    foreach ($off as $dayOff) {
        if ($stamp > $dayOff->start_at && $stamp < $dayOff->end_at) {
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
        let year = calendar.data("year");
        let month = calendar.data("month");
        let day = calendar.data("day");
        window.location.href = "$clockUrl/" + day + "/" + month + "/" + year;
        return false;
    })
    .contextmenu(function () {
        let calendar = $(this);
        let year = calendar.data("year");
        let month = calendar.data("month");
        let day = calendar.data("day");
        window.location.href = "$offUrl/" + day + "/" + month + "/" + year;
        return false;
    });
JS
);
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Calendar') ?></h1>
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['clock/calendar'], 'get'); ?>
            <div class="form-group">
                <?= Html::dropDownList('month', $month, $months, ['class' => 'form-control']) ?>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::textInput('year', $year, ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::submitButton('<i class="glyphicon glyphicon-play"></i>', ['class' => 'btn btn-warning btn-block']) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::a(
                                "<i class=\"glyphicon glyphicon-step-backward\"></i> $previous",
                                ['calendar', 'month' => $previousMonth, 'year' => $previousYear],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::a(
                                "$next <i class=\"glyphicon glyphicon-step-forward\"></i>",
                                ['calendar', 'month' => $nextMonth, 'year' => $nextYear],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group">
            <?= Html::a(
                    '<i class="glyphicon glyphicon-list"></i> ' . Yii::t('app', 'Switch To History'),
                    ['history', 'month' => $month, 'year' => $year],
                    ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="form-group">
            <div class="pull-right">
                <a href="<?= Url::to(['clock/add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-success btn-xs">
                    <i class="glyphicon glyphicon-plus"></i> <?= Yii::t('app', 'Add Session') ?>
                </a>
                <a href="<?= Url::to(['clock/off-add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-warning btn-xs">
                    <i class="glyphicon glyphicon-plus-sign"></i> <?= Yii::t('app', 'Add Off-Time') ?>
                </a>
            </div>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <div class="form-group">
            <div class="calendar day"><?= Yii::t('app', 'Mon') ?></div>
            <div class="calendar day"><?= Yii::t('app', 'Tue') ?></div>
            <div class="calendar day"><?= Yii::t('app', 'Wed') ?></div>
            <div class="calendar day"><?= Yii::t('app', 'Thu') ?></div>
            <div class="calendar day"><?= Yii::t('app', 'Fri') ?></div>
            <div class="calendar day"><?= Yii::t('app', 'Sat') ?></div>
            <div class="calendar day"><?= Yii::t('app', 'Sun') ?></div>
            <div class="clearfix"></div>
            <?php
            $dayOfWeek = $firstDayInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++): ?>
                <div class="calendar selectDay
                    <?= $dayOfWeek > 5 ? 'weekend' : '' ?>
                    <?= in_array($day, $holidays, true) ? 'holiday' : '' ?>
                    <?= in_array($day, $offDays, true) ? 'off' : '' ?>
                    <?= date('Y-m-d') === $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day ? 'today' : '' ?>" style="<?= $day === 1 && $firstDayInMonth !== 1
                    ? 'margin-left:calc(' . (($firstDayInMonth - 1) * 6 + 3) . 'px + ' . (($firstDayInMonth - 1) * 13) . '%'
                    : '' ?>" data-year="<?= $year ?>" data-month="<?= $month ?>" data-day="<?= $day ?>">
                    <?= $day ?>
                    <?php if (!array_key_exists($day, $clockDays)): ?>
                        <p>&nbsp;</p><p>&nbsp;</p>
                    <?php else: ?>
                        <p><small><i class="glyphicon glyphicon-play"></i></small> <?= $clockDays[$day][0] ?></p>
                        <p><small><i class="glyphicon glyphicon-stop"></i></small> <?= $clockDays[$day][1] ?></p>
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
                <i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('app', 'Left-click day to add session. Right-click day to add off-time.') ?>
            </p>
        </div>
    </div>
</div>
