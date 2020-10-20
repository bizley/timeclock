<?php

use app\models\Off;
use app\models\User;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\note\Note;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $employee User
 * @var $user User
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
 * @var $day Off
 * @var $off array
 */

$this->title = Yii::t('app', 'Off-Time');

$total = [];
$list = '';

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Off-Time') ?></h1>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <?= Yii::t('app', 'Month') ?>:
        </div>
        <?= Html::beginForm(['admin/off'], 'get') ?>
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
                            ['off', 'month' => $previousMonth, 'year' => $previousYear, 'id' => $employee !== null ? $employee->id : null],
                            ['class' => 'btn btn-primary']
                        ) ?><?= Html::a(
                            FA::icon('step-forward') . $next,
                            ['off', 'month' => $nextMonth, 'year' => $nextYear, 'id' => $employee !== null ? $employee->id : null],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                </div>
            </div>
        <?= Html::endForm() ?>
        <div class="form-group">
            <?= Html::a(
                FA::icon('list-alt') . ' ' . Yii::t('app', 'Switch To Sessions'),
                ['history', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                ['class' => 'btn btn-warning btn-block']
            ) ?>
        </div>
        <div class="form-group">
            <?= Html::a(
            FA::icon('calendar-alt') . ' ' . Yii::t('app', 'Switch To Calendar'),
                ['calendar', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                ['class' => 'btn btn-info btn-block']
            ) ?>
        </div>
        <div class="form-group">
            <?= Html::a(
                FA::icon('umbrella') . ' ' . Yii::t('app', 'Switch To Projects'),
                ['projects', 'month' => $month, 'year' => $year, 'id' => $employee !== null ? $employee->id : null],
                ['class' => 'btn btn-light btn-block']
            ) ?>
        </div>
        <div class="form-group mb-5">
            <div class="list-group">
                <?php foreach ($users as $user): ?>
                    <a href="<?= Url::to(['off', 'month' => $month, 'year' => $year, 'id' => $user->id]) ?>"
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
                <a href="<?= Url::to(['off', 'month' => $month, 'year' => $year]) ?>" class="btn btn-success btn-sm float-right">
                    <?= FA::icon('users') ?> <?= Yii::t('app', 'All Employees') ?>
                </a>
                <?= Html::encode($employee->name) ?>
            <?php endif; ?>
            <?= $months[$month] ?> <?= $year ?>
        </div>

        <div class="form-group">
            <?= Yii::t('app', 'Vacations Awaiting Approval') ?>
        </div>

        <ul class="list-group mb-3">
            <?php foreach ($off as $day): ?>
                <?php if ($day->type === Off::TYPE_VACATION && $day->approved === 0): ?>
                        <li class="list-group-item">
                            <?= FA::icon('plane') ?>
                            <?= Note::widget(['model' => $day]) ?>
                            <?= Html::encode($users[$day->user_id]->name) ?>
                            <?= Yii::$app->formatter->asDate($day->start_at) ?>
                            <?= FA::icon('long-arrow-alt-right') ?>
                            <?= Yii::$app->formatter->asDate($day->end_at) ?>
                            [<?= Yii::t('app', '{n,plural,one{# day} other{# days}}', ['n' => $day->getWorkDaysOfOffPeriod()]) ?>]
                            <span class="badge badge-danger"><?= FA::icon('exclamation-triangle') ?> <?= Yii::t('app', 'VACATION NOT APPROVED YET') ?></span>
                            <a href="<?= Url::to(['off-deny', 'id' => $day->id]) ?>" class="badge badge-danger float-right ml-1 mt-1"
                                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to deny this vacation?')) ?>>
                                <?= FA::icon('thumbs-down') ?> <?= Yii::t('app', 'deny') ?>
                            </a>
                            <a href="<?= Url::to(['off-approve', 'id' => $day->id]) ?>" class="badge badge-success float-right mt-1"
                                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to approve this vacation?')) ?>>
                                <?= FA::icon('thumbs-up') ?> <?= Yii::t('app', 'approve') ?>
                            </a>
                        </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div class="form-group">
            <?= Yii::t('app', 'Vacations Accepted') ?>
        </div>

        <ul class="list-group mb-3">
            <?php foreach ($off as $day): ?>
                <?php if ($day->type === Off::TYPE_VACATION && $day->approved === 1): ?>
                    <li class="list-group-item">
                        <?= FA::icon('plane') ?>
                        <?= Note::widget(['model' => $day]) ?>
                        <?= Html::encode($users[$day->user_id]->name) ?>
                        <?= Yii::$app->formatter->asDate($day->start_at) ?>
                        <?= FA::icon('long-arrow-alt-right') ?>
                        <?= Yii::$app->formatter->asDate($day->end_at) ?>
                        [<?= Yii::t('app', '{n,plural,one{# day} other{# days}}', ['n' => $day->getWorkDaysOfOffPeriod()]) ?>]
                        <span class="badge badge-success"><?= FA::icon('thumbs-up') ?> <?= Yii::t('app', 'Vacation approved') ?></span>
                        <a href="<?= Url::to(['off-deny', 'id' => $day->id]) ?>" class="badge badge-danger float-right ml-1 mt-1"
                            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to deny this vacation?')) ?>>
                            <?= FA::icon('thumbs-down') ?> <?= Yii::t('app', 'deny') ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div class="form-group">
            <?= Yii::t('app', 'Vacations Denied') ?>
        </div>

        <ul class="list-group mb-3">
            <?php foreach ($off as $day): ?>
                <?php if ($day->type === Off::TYPE_VACATION && $day->approved === 2): ?>
                    <li class="list-group-item">
                        <?= FA::icon('plane') ?>
                        <?= Note::widget(['model' => $day]) ?>
                        <?= Html::encode($users[$day->user_id]->name) ?>
                        <?= Yii::$app->formatter->asDate($day->start_at) ?>
                        <?= FA::icon('long-arrow-alt-right') ?>
                        <?= Yii::$app->formatter->asDate($day->end_at) ?>
                        [<?= Yii::t('app', '{n,plural,one{# day} other{# days}}', ['n' => $day->getWorkDaysOfOffPeriod()]) ?>]
                        <span class="badge badge-secondary"><?= FA::icon('thumbs-down') ?> <?= Yii::t('app', 'Vacation denied') ?></span>
                        <a href="<?= Url::to(['off-approve', 'id' => $day->id]) ?>" class="badge badge-success float-right mt-1"
                            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to approve this vacation?')) ?>>
                            <?= FA::icon('thumbs-up') ?> <?= Yii::t('app', 'approve') ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div class="form-group">
            <?= Yii::t('app', 'Non-Vacations') ?>
        </div>

        <ul class="list-group mb-3">
            <?php foreach ($off as $day): ?>
                <?php if ($day->type !== Off::TYPE_VACATION): ?>
                        <li class="list-group-item">
                            <?= FA::icon('slash') ?>
                            <?= Note::widget(['model' => $day]) ?>
                            <?= Html::encode($users[$day->user_id]->name) ?>
                            <?= Yii::$app->formatter->asDate($day->start_at) ?>
                            <?= FA::icon('long-arrow-alt-right') ?>
                            <?= Yii::$app->formatter->asDate($day->end_at) ?>
                        </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
