<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $session \app\models\Clock */
/* @var $clock array */
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

$this->title = Yii::t('app', 'History');

\yii\bootstrap\BootstrapPluginAsset::register($this);
$this->registerJs('$("[data-toggle=\"tooltip\"]").tooltip();');
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'History') ?></h1>
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['clock/history'], 'get'); ?>
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
                                ['history', 'month' => $previousMonth, 'year' => $previousYear],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::a(
                                "$next <i class=\"glyphicon glyphicon-step-forward\"></i>",
                                ['history', 'month' => $nextMonth, 'year' => $nextYear],
                                ['class' => 'btn btn-primary btn-block']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group">
            <?= Html::a(
                    '<i class="glyphicon glyphicon-calendar"></i> ' . Yii::t('app', 'Switch To Calendar'),
                    ['calendar', 'month' => $month, 'year' => $year],
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
            </div>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <ul class="list-group">
            <?php $total = 0; foreach ($clock as $session): ?>
                <li class="list-group-item">
                    <a href="<?= Url::to(['clock/delete', 'id' => $session->id]) ?>" class="btn btn-danger btn-xs" data-confirm="<?= Yii::t('app', 'Are you sure you want to delete this session?') ?>" data-method="post">
                        <i class="glyphicon glyphicon-remove"></i> <?= Yii::t('app', 'delete') ?>
                    </a>
                    <?= Yii::$app->formatter->asDatetime($session->clock_in) ?>
                    <i class="glyphicon glyphicon-arrow-right"></i>
                    <?php if ($session->clock_out !== null): ?>
                        <?= Yii::$app->formatter->asTime($session->clock_out) ?>
                        <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-time"></i> <?= Yii::t('app', 'edit') ?>
                        </a>
                        <span class="badge"><?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?></span>
                    <?php $total += $session->clock_out - $session->clock_in; else: ?>
                        niezamknięta
                        <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-stop"></i> <?= Yii::t('app', 'end') ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul class="list-group">
            <li class="list-group-item">
                <?= Yii::t('app', 'Total Hours') ?>
                <span class="badge"><?= round($total / 3600, 2) ?></span>
            </li>
        </ul>
        <div class="form-group">
            <div class="pull-right">
                <a href="<?= Url::to(['clock/off-add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-warning btn-xs">
                    <i class="glyphicon glyphicon-plus-sign"></i> <?= Yii::t('app', 'Add Off-Time') ?>
                </a>
            </div>
            <?= Yii::t('app', 'Off-Time') ?>
        </div>
        <ul class="list-group">
            <?php foreach ($off as $day): ?>
                <li class="list-group-item">
                    <?php if ($day->note !== null && $day->note !== ''): ?>
                        <span class="text-info pull-right" data-toggle="tooltip" data-placement="left" title="<?= Html::encode($day->note) ?>"><i class="glyphicon glyphicon-comment"></i></span>
                    <?php endif; ?>
                    <a href="<?= Url::to(['clock/off-delete', 'id' => $day->id]) ?>" class="btn btn-danger btn-xs" data-confirm="<?= Yii::t('app', 'Are you sure you want to delete this off-time?') ?>" data-method="post">
                        <i class="glyphicon glyphicon-remove"></i> <?= Yii::t('app', 'delete') ?>
                    </a>
                    <?= Yii::$app->formatter->asDatetime($day->start_at, 'dd.MM.y') ?>
                    <i class="glyphicon glyphicon-arrow-right"></i>
                    <?= Yii::$app->formatter->asDatetime($day->end_at, 'dd.MM.y') ?>
                    <a href="<?= Url::to(['clock/off-edit', 'id' => $day->id]) ?>" class="btn btn-warning btn-xs">
                        <i class="glyphicon glyphicon-time"></i> <?= Yii::t('app', 'edit') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
