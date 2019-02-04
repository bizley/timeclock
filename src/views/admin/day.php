<?php

use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\bootstrap4\Html;

/**
 * @var $this yii\web\View
 * @var $clock array
 * @var $users array
 * @var $off array
 * @var $day int
 * @var $month int
 * @var $year int
 * @var $employee int
 */

$total = [];
$list = '';
foreach ($clock as $session) {
    if (!isset($total[$session->user_id])) {
        $total[$session->user_id] = 0;
    }

    $list .= '<li class="list-group-item ' . ($employee === $session->user_id ? 'active' : '') . '">';
    if ($session->clock_out !== null) {
        $list .= '<span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">';
        $list .= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) . '</span>';
        $total[$session->user_id] += $session->clock_out - $session->clock_in;
    }
    $list .= Html::encode($users[$session->user_id]->name) . ': ';
    $list .= Yii::$app->formatter->asDatetime($session->clock_in) . ' ';
    $list .= FA::icon('long-arrow-alt-right') . ' ';
    if ($session->clock_out !== null) {
        $list .= Yii::$app->formatter->asTime($session->clock_out);
    } else {
        $list .= Yii::t('app', 'not ended');
    }
    $list .= Note::widget(['model' => $session]);
    $list .= '</li>';
}

?>
<div class="form-group">
    <h4 class="text-center">
        <?= $month ?> <?= $day ?>, <?= $year ?>
    </h4>
</div>
<ul class="list-group mb-3">
    <li class="list-group-item">
        <span class="badge badge-light float-right"><?= round(array_sum($total) / 3600, 2) ?></span>
        <?= Yii::t('app', 'Total Hours') ?>
    </li>
    <?php foreach ($users as $user): ?>
        <li class="list-group-item <?= $employee === $user->id ? 'active' : '' ?>">
            <span class="badge badge-light float-right"><?= isset($total[$user->id]) ? round($total[$user->id] / 3600, 2) : 0 ?></span>
            <?= Html::encode($user->name) ?>
        </li>
    <?php endforeach; ?>
</ul>
<ul class="list-group mb-3"><?= $list ?></ul>
<div class="form-group">
    <?= Yii::t('app', 'Off-Time') ?>
</div>
<ul class="list-group mb-3">
    <?php foreach ($off as $day): ?>
        <li class="list-group-item <?= $employee === $day->user_id ? 'active' : '' ?>">
            <?= Html::encode($users[$day->user_id]->name) ?>
            <?= Yii::$app->formatter->asDate($day->start_at) ?>
            <?= FA::icon('long-arrow-alt-right') ?>
            <?= Yii::$app->formatter->asDate($day->end_at) ?>
            <?= Note::widget(['model' => $day]) ?>
        </li>
    <?php endforeach; ?>
</ul>
