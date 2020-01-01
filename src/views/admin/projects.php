<?php

use app\models\Clock;
use app\models\User;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $employee User
 * @var $user User
 * @var $users array
 * @var $time array
 * @var $months array
 * @var $month int
 * @var $year int
 * @var $previousMonth int
 * @var $previousYear int
 * @var $nextMonth int
 * @var $nextYear int
 * @var $previous string
 * @var $next string
 * @var $week int
 * @var $weekStart int
 * @var $weekEnd int
 * @var $weeksInMonth int
 */

$this->title = Yii::t('app', 'Projects');

$total = [];
$userTime = [];
$userTotal = [];

foreach ($time as $spent) {
    if (!array_key_exists($spent['project_id'], $total)) {
        $total[$spent['project_id']] = 0;
    }

    $total[$spent['project_id']] += $spent['time'];

    if ($employee === null || $employee->id === (int)$spent['user_id']) {
        if (!array_key_exists($spent['user_id'], $userTime)) {
            $userTime[$spent['user_id']] = [];
        }

        if (!array_key_exists($spent['user_id'], $userTotal)) {
            $userTotal[$spent['user_id']] = 0;
        }

        if (!array_key_exists($spent['project_id'], $userTime[$spent['user_id']])) {
            $userTime[$spent['user_id']][$spent['project_id']] = 0;
        }

        $userTime[$spent['user_id']][$spent['project_id']] += $spent['time'];
        $userTotal[$spent['user_id']] += $spent['time'];
    }
}

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Projects') ?></h1>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['admin/projects'], 'get') ?>
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
                            ['projects', 'month' => $previousMonth, 'year' => $previousYear, 'id' => $employee !== null ? $employee->id : null],
                            ['class' => 'btn btn-primary']
                        ) ?><?= Html::a(
                            FA::icon('step-forward') . $next,
                            ['projects', 'month' => $nextMonth, 'year' => $nextYear, 'id' => $employee !== null ? $employee->id : null],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm() ?>
        <div class="form-group">
            <?= Html::a(
                FA::icon('list-alt') . ' ' . Yii::t('app', 'Switch To Sessions'),
                ['history', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null, 'week' => $week],
                ['class' => 'btn btn-warning btn-block']
            ) ?>
        </div>
        <div class="form-group">
            <?= Html::a(
                FA::icon('plane') . ' ' . Yii::t('app', 'Switch To Off-Time'),
                ['off', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                ['class' => 'btn btn-success btn-block']
            ) ?>
        </div>
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
                    <a href="<?= Url::to(['projects', 'month' => $month, 'year' => $year, 'id' => $user->id, 'week' => $week]) ?>"
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
                <a href="<?= Url::to(['projects', 'month' => $month, 'year' => $year]) ?>" class="btn btn-success btn-sm float-right">
                    <?= FA::icon('users') ?> <?= Yii::t('app', 'All Employees') ?>
                </a>
                <?= Html::encode($employee->name) ?>
            <?php endif; ?>
            <?= $months[$month] ?> <?= $year ?>
        </div>

        <?php if ($weekStart !== null && $weekEnd !== null): ?>
            <div class="form-group">
                <?= FA::icon('calendar-day') ?>
                <strong>
                    <?= Yii::t('app', 'Days') ?>:
                    <?= $weekStart ?>
                    <span class="badge badge-primary"><?= Clock::days()[date('N', mktime(6, 0, 0, $month, $weekStart, $year))] ?></span>
                    -
                    <?= $weekEnd ?>
                    <span class="badge badge-primary"><?= Clock::days()[date('N', mktime(6, 0, 0, $month, $weekEnd, $year))] ?></span>
                </strong>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <?php if ($week !== null): ?>
                <a href="<?= Url::to(['projects', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null]) ?>"
                   class="btn btn-outline-success btn-sm float-right ml-3">
                    <?= Yii::t('app', 'Month View') ?>
                </a>
            <?php endif; ?>
            <?php for ($w = 1; $w <= $weeksInMonth; $w++): ?>
                <a href="<?= Url::to(['projects', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null, 'week' => $w]) ?>"
                   class="btn btn-<?= $w === $week ? '' : 'outline-' ?>primary btn-sm">
                    <?= Yii::t('app', 'WEEK') ?> <?= $w ?>
                </a>
            <?php endfor; ?>
        </div>

        <div class="form-group">
            <?= Yii::t('app', 'Total Hours') ?>
        </div>
        <ul class="list-group mb-3">
            <?php $i = 1; foreach ($total as $project => $spent): ?>
                <li class="list-group-item">
                    <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                        <?= round($spent / 3600, 2) ?>
                    </span>
                    <?= $i++ ?>.
                    <span class="badge project-badge" style="background-color:<?= $projects[$project]['color'] ?? 'transparent' ?>">&nbsp;&nbsp;</span>
                    <?= Html::encode($projects[$project]['name'] ?? '?') ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php
        foreach ($userTime as $userId => $userSpent):
            if ($employee !== null && (int)$userId !== $employee->id) {
                continue;
            } ?>
            <div class="form-group">
                <?= Html::encode($users[$userId]->name) ?>
                <span class="text-muted">[<?= Yii::t('app', 'participation in project') ?> / <?= Yii::t('app', 'participation in own time') ?>]</span>
            </div>
            <ul class="list-group mb-3">
                <?php $i = 1; foreach ($userSpent as $project => $spent): ?>
                    <li class="list-group-item">
                        <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                            <?= round($spent / 3600, 2) ?>
                        </span>
                        <?= $i++ ?>.
                        <span class="badge project-badge" style="background-color:<?= $projects[$project]['color'] ?? 'transparent' ?>">&nbsp;&nbsp;</span>
                        [<?= (int)$total[$project] > 0 ? round($spent * 100 / (int)$total[$project], 1) : 0 ?>% /
                        <?= (int)$userTotal[$userId] > 0 ? round($spent * 100 / (int)$userTotal[$userId], 1) : 0 ?>%]
                        <?= Html::encode($projects[$project]['name'] ?? '?') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>

    </div>
</div>
