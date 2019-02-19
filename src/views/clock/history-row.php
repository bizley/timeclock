<?php

use app\models\Clock;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\helpers\Url;

/* @var $session Clock */
/* @var $day string */

?>
<li class="list-group-item day<?= $day ?>" style="<?= $day ? 'display:none; opacity:0' : '' ?>">
    <?php if ($session->clock_out !== null): ?>
        <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
            <?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?>
        </span>
        <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-outline-warning btn-sm float-left mr-1">
            <?= FA::icon('clock') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'edit') ?></span>
        </a>
    <?php else: ?>
        <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-outline-success btn-sm float-left mr-1">
            <?= FA::icon('clock') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'edit') ?></span>
        </a>
    <?php endif; ?>
    <a href="<?= Url::to(['clock/delete', 'id' => $session->id, 'stay' => true]) ?>"
       class="btn btn-outline-danger btn-sm"
        <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this session?')) ?>>
        <?= FA::icon('times') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'delete') ?></span>
    </a>
    <?= Yii::$app->formatter->asDatetime($session->clock_in) ?>
    <?= FA::icon('long-arrow-alt-right') ?>
    <?php if ($session->clock_out !== null): ?>
        <?= Yii::$app->formatter->asTime($session->clock_out) ?>
    <?php else: ?>
        <?= Yii::t('app', 'not ended') ?>
    <?php endif; ?>
    <?= Note::widget(['model' => $session]) ?>
</li>
