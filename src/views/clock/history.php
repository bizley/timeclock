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

$this->title = 'Company Timeclock | Historia';
?>
<div class="form-group">
    <h1>Historia</h1>
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            Miesiąc:
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
                        <?= Html::a("<i class=\"glyphicon glyphicon-step-backward\"></i> $previous", ['history', 'month' => $previousMonth, 'year' => $previousYear], ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::a("$next <i class=\"glyphicon glyphicon-step-forward\"></i>", ['history', 'month' => $nextMonth, 'year' => $nextYear], ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm(); ?>
        <div class="form-group">
            <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> Przełącz na kalendarz', ['calendar', 'month' => $month, 'year' => $year], ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="form-group">
            <div class="pull-right">
                <a href="<?= Url::to(['clock/add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-success btn-xs">
                    <i class="glyphicon glyphicon-plus"></i> Dodaj sesję
                </a>
            </div>
            <?= $months[$month] ?> <?= $year ?>
        </div>
        <ul class="list-group">
            <?php $total = 0; foreach ($clock as $session): ?>
                <li class="list-group-item">
                    <a href="<?= Url::to(['clock/delete', 'id' => $session->id]) ?>" class="btn btn-danger btn-xs" data-confirm="Czy na pewno chcesz usunąć tę sesję?" data-method="post">
                        <i class="glyphicon glyphicon-remove"></i> usuń
                    </a>
                    <?= Yii::$app->formatter->asDatetime($session->clock_in) ?>
                    <i class="glyphicon glyphicon-arrow-right"></i>
                    <?php if ($session->clock_out !== null): ?>
                        <?= Yii::$app->formatter->asTime($session->clock_out) ?>
                        <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-time"></i> zmień
                        </a>
                        <span class="badge"><?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?></span>
                    <?php $total += $session->clock_out - $session->clock_in; else: ?>
                        niezamknięta
                        <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-stop"></i> zamknij
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul class="list-group">
            <li class="list-group-item">
                Razem godzin
                <span class="badge"><?= round($total / 3600, 2) ?></span>
            </li>
        </ul>
        <div class="form-group">
            <div class="pull-right">
                <a href="<?= Url::to(['clock/off-add', 'year' => $year, 'month' => $month]) ?>" class="btn btn-warning btn-xs">
                    <i class="glyphicon glyphicon-plus-sign"></i> Dodaj wolne
                </a>
            </div>
            Wolne
        </div>
        <ul class="list-group">
            <?php foreach ($off as $day): ?>
                <li class="list-group-item">
                    <a href="<?= Url::to(['clock/off-delete', 'id' => $day->id]) ?>" class="btn btn-danger btn-xs" data-confirm="Czy na pewno chcesz usunąć to wolne?" data-method="post">
                        <i class="glyphicon glyphicon-remove"></i> usuń
                    </a>
                    <?= Yii::$app->formatter->asDatetime($day->start_at, 'dd.MM.y') ?>
                    <i class="glyphicon glyphicon-arrow-right"></i>
                    <?= Yii::$app->formatter->asDatetime($day->end_at, 'dd.MM.y') ?>
                    <a href="<?= Url::to(['clock/off-edit', 'id' => $day->id]) ?>" class="btn btn-warning btn-xs">
                        <i class="glyphicon glyphicon-time"></i> zmień
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
