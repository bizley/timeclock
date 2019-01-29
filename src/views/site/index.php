<?php

use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\modal\Clock;
use app\widgets\note\Note;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $session \app\models\Clock */
/* @var $user \app\models\User */

$this->title = 'Timeclock';
?>
<h1><?= date('Y-m-d') ?></h1>

<div class="row">
    <div class="col-sm-4">
        <?php if ($user->isClockActive()): ?>
            <div class="form-group">
                <?= Yii::t('app', 'Session started at {time}', ['time' => Yii::$app->formatter->asTime($user->sessionStartedAt())]) ?>
            </div>
            <div class="form-group">
                <a href="<?= Url::to(['clock/stop']) ?>"
                   class="btn btn-danger btn-lg btn-block clock"
                    <?= Confirm::ask(Yii::t('app', 'Are you sure you want to end this session?')) ?>>
                    <?= FA::icon('stop') ?>
                    <?= Yii::t('app', 'End Session') ?>
                </a>
            </div>
        <?php else: ?>
            <div class="form-group">
                <?= Clock::button() ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-8">
        <div class="form-group">
            <?= Yii::t('app', 'Today Sessions') ?>:
        </div>
        <?php
        $todays = $user->todaysSessions();
        $now = time();
        if ($todays): ?>
            <div class="form-group">
                <ul class="list-group">
                    <?php foreach ($todays as $session): ?>
                        <li class="list-group-item">
                            <?= Note::widget(['model' => $session]) ?>
                            <?= Yii::$app->formatter->asTime($session->clock_in) ?>
                            <?= FA::icon('long-arrow-alt-right') ?>
                            <?php if ($session->clock_out !== null): ?>
                                <span class="badge badge-light float-right"><?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?></span>
                                <?= Yii::$app->formatter->asTime($session->clock_out) ?>
                                <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-outline-warning btn-sm">
                                    <?= FA::icon('clock') ?> <?= Yii::t('app', 'edit') ?>
                                </a>
                            <?php else: ?>
                                <span class="badge badge-light float-right"><?= Yii::$app->formatter->asDuration($now - $session->clock_in) ?></span>
                                <?= Yii::t('app', 'on-going') ?>
                                <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="btn btn-outline-success btn-sm">
                                    <?= FA::icon('clock') ?> <?= Yii::t('app', 'edit') ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="form-group"><?= Yii::t('app', 'NONE') ?></div>
        <?php endif; ?>
        <?php $oldestOpened = $user->getOldOpenedSession(); if ($oldestOpened): ?>
            <div class="form-group">
                <a href="<?= Url::to([
                    'clock/history',
                    'm' => Yii::$app->formatter->asDate($oldestOpened->clock_in, 'M'),
                    'y' => Yii::$app->formatter->asDate($oldestOpened->clock_in, 'y'),
                ]) ?>" class="btn btn-danger"><?= FA::icon('exclamation-triangle') ?> <?= Yii::t('app', 'Old sessions have not been ended') ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>
