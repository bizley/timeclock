<?php

use app\models\Clock;
use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\bootstrap4\Html;

/* @var $session Clock */
/* @var $day string */
/* @var $users array */

?>
<li class="list-group-item day<?= $day ?>" style="<?= $day ? 'display:none; opacity:0' : '' ?>">
    <?php if ($session->clock_out !== null): ?>
        <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
            <?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?>
        </span>
    <?php endif; ?>
    <?= Html::encode($users[$session->user_id]->name) ?>:
    <?= Yii::$app->formatter->asDatetime($session->clock_in) ?>
    <?= FA::icon('long-arrow-alt-right') ?>
    <?php if ($session->clock_out !== null): ?>
        <?= Yii::$app->formatter->asTime($session->clock_out) ?>
    <?php else: ?>
        <?= Yii::t('app', 'not ended') ?>
    <?php endif; ?>
    <?php if ($session->project_id): ?>
        <span class="badge project-badge ml-1" style="background-color:<?= $session->project->color ?>"><?= Html::encode($session->project->name) ?></span>
    <?php endif; ?>
    <?= Note::widget(['model' => $session]) ?>
</li>
