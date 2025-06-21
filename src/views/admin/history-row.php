<?php

use app\models\Clock;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\bootstrap4\Html;
use yii\helpers\Url;

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
    <?php if (Yii::$app->params['adminSessionEdit']): ?>
        <a href="<?= Url::to(['admin/session-edit', 'id' => $session->id, 'user_id' => $session->user_id]) ?>" class="action badge badge-<?= $session->clock_out !== null ? 'warning' : 'success' ?> ml-1">
            <?= FA::icon('clock') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'edit') ?></span>
        </a>
    <?php endif; ?>
    <?php if (Yii::$app->params['adminSessionDelete']): ?>
        <a href="<?= Url::to(['admin/session-delete', 'id' => $session->id, 'user_id' => $session->user_id, 'stay' => true]) ?>" class="action badge badge-danger ml-1 mr-1"
            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this session?')) ?>>
            <?= FA::icon('times') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'delete') ?></span>
        </a>
    <?php endif; ?>
    <?= Note::widget(['model' => $session]) ?>
</li>
