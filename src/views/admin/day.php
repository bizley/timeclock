<?php

use app\widgets\note\Note;
use yii\bootstrap\Html;

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

?>
<div class="form-group">
    <h4 class="text-center">
        <?= $month ?> <?= $day ?>, <?= $year ?>
    </h4>
</div>
<ul class="list-group">
    <li class="list-group-item">
        <?= Yii::t('app', 'Total Hours') ?>
        <span class="badge"><?= round(array_sum($total) / 3600, 2) ?></span>
    </li>
    <?php foreach ($users as $user): ?>
        <li class="list-group-item <?= $employee === $user->id ? 'active' : '' ?>">
            <?= Html::encode($user->name) ?>
            <span class="badge"><?= isset($total[$user->id]) ? round($total[$user->id] / 3600, 2) : 0 ?></span>
        </li>
    <?php endforeach; ?>
</ul>
<ul class="list-group"><?= $list ?></ul>
<div class="form-group">
    <?= Yii::t('app', 'Off-Time') ?>
</div>
<ul class="list-group">
    <?php foreach ($off as $day): ?>
        <li class="list-group-item <?= $employee === $day->user_id ? 'active' : '' ?>">
            <?= Note::widget(['offtime' => $day]) ?>
            <?= Html::encode($users[$day->user_id]->name) ?>
            <?= Yii::$app->formatter->asDatetime($day->start_at, 'dd.MM.y') ?>
            <i class="glyphicon glyphicon-arrow-right"></i>
            <?= Yii::$app->formatter->asDatetime($day->end_at, 'dd.MM.y') ?>
        </li>
    <?php endforeach; ?>
</ul>
