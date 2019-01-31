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
foreach ($clock as $session) {
    if (!isset($total[$session->user_id])) {
        $total[$session->user_id] = 0;
    }

    $list .= '<li class="list-group-item">';
    if ($session->clock_out !== null) {
        $list .= '<span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">';
        $list .= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) . '</span>';
    }
    $list .= Html::encode($users[$session->user_id]->name) . ': ';
    $list .= Yii::$app->formatter->asDatetime($session->clock_in) . ' ';
    $list .= FA::icon('long-arrow-alt-right') . ' ';
    if ($session->clock_out !== null) {
        $list .= Yii::$app->formatter->asTime($session->clock_out);
        $total[$session->user_id] += $session->clock_out - $session->clock_in;
    } else {
        $list .= Yii::t('app', 'not ended');
    }
    $list .= Note::widget(['model' => $session]);
    $list .= '</li>';
}

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
