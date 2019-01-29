<?php

use app\widgets\modal\Day;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $entries array
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
 * @var $firstDayInMonth int
 * @var $daysInMonth int
 * @var $holidays array
 */

$this->title = Yii::t('app', 'Overall Calendar');
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Overall Calendar') ?></h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['clock/calendar'], 'get'); ?>
            <?= Html::hiddenInput('id', $employee !== null ? $employee->id : null) ?>
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
                                ['calendar', 'month' => $previousMonth, 'year' => $previousYear, 'id' => $employee !== null ? $employee->id : null],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::a(
                                "$next <i class=\"glyphicon glyphicon-step-forward\"></i>",
                                ['calendar', 'month' => $nextMonth, 'year' => $nextYear, 'id' => $employee !== null ? $employee->id : null],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group">
            <?= Html::a(
                    '<i class="glyphicon glyphicon-list"></i> ' . Yii::t('app', 'Switch To History'),
                    ['history', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                    ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
        <div class="form-group">
            <div class="list-group">
                <?php foreach ($users as $user): ?>
                    <a href="<?= Url::to(['calendar', 'month' => $month, 'year' => $year, 'id' => $user->id]) ?>" class="list-group-item <?= $employee !== null && $employee->id === $user->id ? 'active' : '' ?>">
                        <?= Html::encode($user->name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="form-group">
            <?php if ($employee !== null): ?>
                <a href="<?= Url::to(['calendar', 'month' => $month, 'year' => $year]) ?>" class="btn btn-success btn-xs pull-right"><?= Yii::t('app', 'All Employees') ?></a>
                <?= Html::encode($employee->name) ?>
            <?php endif; ?>
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
                <div class="fixed calendar
                    <?= $dayOfWeek > 5 ? 'weekend' : '' ?>
                    <?= in_array($day, $holidays, true) ? 'holiday' : '' ?>
                    <?= date('Y-m-d') === $year . '-' . ($month < 10 ? '0' : '') . $month . '-' . ($day < 10 ? '0' : '') . $day ? 'today' : '' ?>" style="<?= $day === 1 && $firstDayInMonth !== 1
                    ? 'margin-left:calc(' . (($firstDayInMonth - 1) * 6 + 3) . 'px + ' . (($firstDayInMonth - 1) * 13) . '%'
                    : '' ?>">
                    <?= $day ?>
                    <?php if (array_key_exists($day, $entries)): ?>
                        <p>
                            <?php foreach ($entries[$day] as $userId => $initials): ?>
                                <?= Day::add($initials, $day, $month, $year, $userId) ?>
                            <?php endforeach; ?>
                        </p>
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
                <span class="btn btn-primary btn-xs">&nbsp;&nbsp;</span> <?= Yii::t('app', 'Click the initials to see day details.') ?>
            </p>
        </div>
    </div>
</div>
