<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $session \app\models\Clock */
/* @var $clock array */
/* @var $employee \app\models\User */
/* @var $user \app\models\User */
/* @var $users array */
/* @var $months array */
/* @var $month int */
/* @var $year int */
/* @var $previousMonth int */
/* @var $previousYear int */
/* @var $nextMonth int */
/* @var $nextYear int */
/* @var $previous string */
/* @var $next string */
/* @var $day \app\models\Off */
/* @var $off array */

$this->title = Yii::t('app', 'Sessions');

$total = [];
$list = '';
foreach ($clock as $session) {
    if (!isset($total[$session->user_id])) {
        $total[$session->user_id] = 0;
    }

    $list .= '<li class="list-group-item">';
    $list .= Html::encode($users[$session->user_id]->name) . ' ';
    $list .= Yii::$app->formatter->asDatetime($session->clock_in) . ' ';
    $list .= '<i class="glyphicon glyphicon-arrow-right"></i>' . ' ';
    if ($session->clock_out !== null) {
        $list .= Yii::$app->formatter->asTime($session->clock_out);
        $list .= '<span class="badge">' . Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) . '</span>';
        $total[$session->user_id] += $session->clock_out - $session->clock_in;
    } else {
        $list .= Yii::t('app', 'not ended');
    }
    $list .= '</li>';
}

\yii\bootstrap\BootstrapPluginAsset::register($this);
$this->registerJs('$("[data-toggle=\"tooltip\"]").tooltip();');
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Sessions') ?></h1>
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['admin/history'], 'get'); ?>
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
                                ['history', 'month' => $previousMonth, 'year' => $previousYear, 'id' => $employee !== null ? $employee->id : null],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::a(
                                "$next <i class=\"glyphicon glyphicon-step-forward\"></i>",
                                ['history', 'month' => $nextMonth, 'year' => $nextYear, 'id' => $employee !== null ? $employee->id : null],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group">
            <?= Html::a(
                    '<i class="glyphicon glyphicon-calendar"></i> ' . Yii::t('app', 'Switch To Calendar'),
                    ['calendar', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                    ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
        <div class="form-group">
            <div class="list-group">
                <?php foreach ($users as $user): ?>
                    <a href="<?= Url::to(['history', 'month' => $month, 'year' => $year, 'id' => $user->id]) ?>" class="list-group-item <?= $employee !== null && $employee->id === $user->id ? 'active' : '' ?>">
                        <?= Html::encode($user->name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="form-group">
            <?php if ($employee !== null): ?>
                <a href="<?= Url::to(['history', 'month' => $month, 'year' => $year]) ?>" class="btn btn-success btn-xs pull-right"><?= Yii::t('app', 'All Employees') ?></a>
                <?= Html::encode($employee->name) ?>
            <?php endif; ?>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <ul class="list-group">
            <li class="list-group-item">
                <?= Yii::t('app', 'Total Hours') ?>
                <span class="badge"><?= round(array_sum($total) / 3600, 2) ?></span>
            </li>
            <?php if ($employee === null): ?>
                <?php foreach ($users as $user): ?>
                    <li class="list-group-item">
                        <?= Html::encode($user->name) ?>
                        <span class="badge"><?= isset($total[$user->id]) ? round($total[$user->id] / 3600, 2) : 0 ?></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <ul class="list-group"><?= $list ?></ul>
        <div class="form-group">
            <?= Yii::t('app', 'Off-Time') ?>
        </div>
        <ul class="list-group">
            <?php foreach ($off as $day): ?>
                <li class="list-group-item">
                    <?php if ($day->note !== null && $day->note !== ''): ?>
                        <span class="text-info pull-right" data-toggle="tooltip" data-placement="left" title="<?= Html::encode($day->note) ?>"><i class="glyphicon glyphicon-comment"></i></span>
                    <?php endif; ?>
                    <?= Html::encode($users[$day->user_id]->name) ?>
                    <?= Yii::$app->formatter->asDatetime($day->start_at, 'dd.MM.y') ?>
                    <i class="glyphicon glyphicon-arrow-right"></i>
                    <?= Yii::$app->formatter->asDatetime($day->end_at, 'dd.MM.y') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
