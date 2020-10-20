<?php

use app\models\Off;
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
 * @var $offDay Off
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
    if ($session->project_id) {
        $list .= ' <span class="badge project-badge" style="background-color:' . $session->project->color . '">' . Html::encode($session->project->name) . '</span>';
    }
    $list .= Note::widget(['model' => $session]);
    $list .= '</li>';
}

?>
<div class="form-group">
    <h4 class="text-center">
        <?= Yii::$app->formatter->asDate($year . '-' . $month . '-' . $day, 'long'); ?>
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
    <?php foreach ($off as $offDay): ?>
        <li class="list-group-item <?= $employee === $offDay->user_id ? 'active' : '' ?>">
            <?php if ($offDay->type === Off::TYPE_VACATION): ?>
                <?php if ($offDay->approved === 0): ?>
                    <span class="badge badge-danger float-right"><?= FA::icon('exclamation-triangle') ?> <?= Yii::t('app', 'VACATION NOT APPROVED YET') ?></span>
                <?php elseif ($offDay->approved === 1): ?>
                    <span class="badge badge-success float-right"><?= FA::icon('thumbs-up') ?> <?= Yii::t('app', 'Vacation approved') ?></span>
                <?php else: ?>
                    <span class="badge badge-secondary float-right"><?= FA::icon('thumbs-down') ?> <?= Yii::t('app', 'Vacation denied') ?></span>
                <?php endif; ?>
                <?= FA::icon('plane') ?>
            <?php else: ?>
                <?= FA::icon('slash') ?>
            <?php endif; ?>
            <?= Html::encode($users[$offDay->user_id]->name) ?>
            <?= Yii::$app->formatter->asDate($offDay->start_at) ?>
            <?= FA::icon('long-arrow-alt-right') ?>
            <?= Yii::$app->formatter->asDate($offDay->end_at) ?>
            <?= Note::widget(['model' => $offDay]) ?>
        </li>
    <?php endforeach; ?>
</ul>
