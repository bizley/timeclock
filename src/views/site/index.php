<?php

use app\models\Off;
use app\models\User;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\modal\Clock;
use app\widgets\note\Note;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $session \app\models\Clock
 * @var $user User
 * @var $nextVacation Off
 * @var $vacationDays int
 */

$this->title = 'Timeclock';

$this->registerJs(<<<JS
let renderTime = function (clock) {
    let seconds = clock.data("time");
    let display;
    
    if (seconds < 0) {
        seconds = 0;
    }

    if (seconds < 60) {
        display = "00:00:" + (seconds < 10 ? 0 : "") + seconds;
    } else {
        let minutes = Math.floor(seconds / 60);
        seconds %= 60;
    
        if (minutes < 60) {
            display = "00:" + (minutes < 10 ? 0 : "") + minutes + ":" + (seconds < 10 ? 0 : "") + seconds;
        } else {
            let hours = Math.floor(minutes / 60);
            minutes %= 60;
        
            display = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? 0 : "") + minutes + ":" + (seconds < 10 ? 0 : "") + seconds;
        }
    }
    
    clock.text(display);
};
let tikTak = function(clock) {
    let timeValue = parseInt(clock.data("time"));
    clock.data("time", ++timeValue);
    
    renderTime(clock);
};

$(".running-clock").each(function() {
    let clock = $(this);
    window.setInterval(function() {
        tikTak(clock);
    }, 1000);
});
JS
);

$assignedProjects = $user->assignedProjects;
$sessionStarted = $user->latestSession();
$isActive = $user->isClockActive();

$todays = $user->todaysSessions();
$sessionsTime = 0;
$lastStart = 0;
$projectsTime = [];

foreach ($todays as $session) {
    if ($session->project_id !== null && !isset($projectsTime[$session->project_id])) {
        $projectsTime[$session->project_id] = [
            'total' => 0,
            'lastStart' => 0,
        ];
    }

    if ($session->clock_out !== null) {
        $sessionsTime += $session->clock_out - $session->clock_in;

        if ($session->project_id !== null) {
            $projectsTime[$session->project_id]['total'] += $session->clock_out - $session->clock_in;
        }
    } elseif ($session->clock_in > $lastStart) {
        $lastStart = $session->clock_in;

        if ($session->project_id !== null && $session->clock_in > $projectsTime[$session->project_id]['lastStart']) {
            $projectsTime[$session->project_id]['lastStart'] = $session->clock_in;
        }
    }
}

$now = time();

?>
<h1><?= Yii::$app->formatter->asDate(date('Y-m-d')) ?></h1>

<div class="row">
    <div class="col-lg-4">
        <?php if ($isActive): ?>
            <div class="form-group">
                <?= Yii::t('app', 'Session started at {time}', ['time' => Yii::$app->formatter->asTime($user->sessionStartedAt())]) ?>
            </div>
            <div class="form-group">
                <a href="<?= Url::to(['clock/stop']) ?>"
                   class="btn btn-danger btn-lg btn-block clock"
                    <?= Confirm::ask(Yii::t('app', 'Are you sure you want to end this session?')) ?>>
                    <?= FA::icon('clock fa-spin') ?>
                    <?= Yii::t('app', 'End Session') ?>
                </a>
            </div>
        <?php else: ?>
            <div class="form-group">
                <?= Clock::button() ?>
            </div>
        <?php endif; ?>

        <?php if ($assignedProjects): ?>
            <div class="form-group text-center">
                <?= Yii::t('app', 'Switch Session To') ?>:
            </div>
            <div class="form-group">
                <div class="list-group">
                    <?php foreach ($assignedProjects as $id => $name): ?>
                    <a href="<?= Url::to(['switch-project', 'id' => $id]) ?>"
                       class="list-group-item <?= $sessionStarted !== null && (int)$id === $sessionStarted->project_id ? 'disabled' : '' ?>"
                        <?= Confirm::ask(Yii::t('app', 'Are you sure you want to switch current session?')) ?>>
                        <?php if (isset($projectsTime[$id])): ?>
                            <?php if ($projectsTime[$id]['lastStart'] > 0): ?>
                                <span class="badge badge-dark float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                                    <span class="running-clock" data-time="<?= $now - $projectsTime[$id]['lastStart'] + $projectsTime[$id]['total'] ?>">00:00:00</span>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                                    <?= Yii::$app->formatter->asDuration($projectsTime[$id]['total']) ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span class="badge" style="background-color:<?= $projects[$id]['color'] ?? 'transparent' ?>">&nbsp;&nbsp;</span>
                        <?= Html::encode($name) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-8">
        <div class="form-group">
            <?= Yii::t('app', 'Today Sessions') ?>:
        </div>
        <?php if ($todays): ?>
            <div class="form-group">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                            <?php if ($isActive): ?>
                                <span class="running-clock" data-time="<?= $now - $lastStart + $sessionsTime ?>">00:00:00</span>
                            <?php else: ?>
                                <?= Yii::$app->formatter->asDuration($sessionsTime) ?>
                            <?php endif; ?>
                        </span>
                        <?= Yii::t('app', 'Total Hours') ?>
                    </li>
                </ul>
            </div>
            <div class="form-group">
                <ul class="list-group">
                    <?php foreach ($todays as $session): ?>
                        <li class="list-group-item">
                            <span class="badge badge-light float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                                <?php if ($session->clock_out !== null): ?>
                                    <?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?>
                                <?php else: ?>
                                    <span class="running-clock" data-time="<?= $now - $session->clock_in ?>">00:00:00</span>
                                <?php endif; ?>
                            </span>
                            <?= Yii::$app->formatter->asTime($session->clock_in) ?>
                            <?= FA::icon('long-arrow-alt-right') ?>
                            <?php if ($session->clock_out !== null): ?>
                                <?= Yii::$app->formatter->asTime($session->clock_out) ?>
                            <?php else: ?>
                                <?= Yii::t('app', 'on-going') ?>
                            <?php endif; ?>
                            <?php if ($session->project_id): ?>
                                <span class="badge project-badge ml-1" style="background-color:<?= $session->project->color ?>"><?= Html::encode($session->project->name) ?></span>
                            <?php endif; ?>
                            <a href="<?= Url::to(['clock/edit', 'id' => $session->id]) ?>" class="action badge badge-<?= $session->clock_out !== null ? 'warning' : 'success' ?> ml-1">
                                <?= FA::icon('clock') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'edit') ?></span>
                            </a>
                            <a href="<?= Url::to(['clock/delete', 'id' => $session->id]) ?>" class="action badge badge-danger ml-1 mr-1"
                                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this session?')) ?>>
                                <?= FA::icon('times') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'delete') ?></span>
                            </a>
                            <?= Note::widget(['model' => $session]) ?>
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
                    'month' => Yii::$app->formatter->asDate($oldestOpened->clock_in, 'M'),
                    'year' => Yii::$app->formatter->asDate($oldestOpened->clock_in, 'y'),
                ]) ?>" class="btn btn-warning">
                    <?= FA::icon('exclamation-triangle') ?> <?= Yii::t('app', 'Old sessions have not been ended') ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?= Yii::t('app', 'Incoming Vacation') ?>:
        </div>
        <div class="form-group">
            <ul class="list-group">
                <?php if ($nextVacation): ?>
                    <li class="list-group-item">
                        <?php if ($nextVacation->approved === 0): ?>
                            <span class="badge badge-danger float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                                <?= FA::icon('exclamation-triangle') ?> <?= Yii::t('app', 'vacation awaits approval') ?>
                            </span>
                        <?php elseif ($nextVacation->approved === 1): ?>
                            <span class="badge badge-success float-sm-right d-block d-sm-inline mb-2 ml-0 ml-sm-3">
                                <?= FA::icon('thumbs-up') ?> <?= Yii::t('app', 'vacation approved') ?>
                            </span>
                        <?php endif; ?>
                        <?= Yii::$app->formatter->asDate($nextVacation->start_at) ?>
                        <?= FA::icon('long-arrow-alt-right') ?>
                        <?= Yii::$app->formatter->asDate($nextVacation->end_at) ?>
                        [<?= Yii::t('app', '{n,plural,one{# day} other{# days}}', ['n' => $nextVacation->getWorkDaysOfOffPeriod()]) ?>]
                        <a href="<?= Url::to(['clock/off-edit', 'id' => $nextVacation->id]) ?>" class="action badge badge-warning ml-1">
                            <?= FA::icon('clock') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'edit') ?></span>
                        </a>
                        <a href="<?= Url::to(['clock/off-delete', 'id' => $nextVacation->id]) ?>"
                           class="action badge badge-danger ml-1 mr-1"
                            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this off-time?')) ?>>
                            <?= FA::icon('times') ?> <span class="d-none d-md-inline"><?= Yii::t('app', 'delete') ?></span>
                        </a>
                        <?= Note::widget(['model' => $nextVacation]) ?>
                    </li>
                <?php else: ?>
                    <li class="list-group-item small"><?= Yii::t('app', 'No planned or accepted vacation :(') ?></li>
                <?php endif; ?>
                <li class="list-group-item small">
                    <span class="float-right"><?= Yii::t('app', '{n,plural,one{# day} other{# days}}', ['n' => $vacationDays]) ?></span>
                    <?= Yii::t('app', 'This years\' accepted vacation in total:') ?>
                </li>
            </ul>
        </div>
    </div>
</div>
