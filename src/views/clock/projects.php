<?php

use app\base\ClockHelper;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $session \app\models\Clock
 * @var $projects array
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
 */

$this->title = Yii::t('app', 'Projects');

$spent = [];
$total = 0;
foreach ($time as $projectTime) {
    $spent[$projectTime['project_id']] = [
        'seconds' => $projectTime['time'],
        'desc' => ClockHelper::as8HrsDayDuration((int)$projectTime['time'])
    ];
    $total += $projectTime['time'];
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
        <?= Html::beginForm(['clock/projects'], 'get'); ?>
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
                            ['projects', 'month' => $previousMonth, 'year' => $previousYear],
                            ['class' => 'btn btn-primary']
                        ) ?><?= Html::a(
                            FA::icon('step-forward') . $next,
                            ['projects', 'month' => $nextMonth, 'year' => $nextYear],
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
            <?php $i = 1; foreach ($spent as $projectId => $projectTime): ?>
                <li class="list-group-item">
                    <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                        <?= $projectTime['desc'] ?>
                    </span>
                    <?= $i++ ?>.
                    <span class="badge project-badge" style="background-color:<?= $projects[$projectId]['color'] ?? 'transparent' ?>">&nbsp;&nbsp;</span>
                    [<?= $total > 0 ? round($projectTime['seconds'] * 100 / $total, 1) : 0 ?>%]
                    <?= Html::encode($projects[$projectId]['name'] ?? '?') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
