<?php

use app\models\Clock;
use app\models\ClockForm;
use app\widgets\confirm\Confirm;
use app\widgets\date\DatePicker;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/**
 * @var $this yii\web\View
 * @var $session Clock
 * @var $model ClockForm
 * @var $projects array
 */

$this->title = Yii::t('app', 'Editing Session');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Editing Session') ?></h1>
</div>

<div class="row">
    <div class="col-lg-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-outline-primary btn-block"><?= FA::icon('backward') ?> <?= Yii::t('app', 'Go Back') ?></a>
        </div>
        <div class="form-group">
            <a href="<?= Url::to(['admin/session-delete', 'id' => $session->id, 'user_id' => $session->user_id]) ?>"
               class="btn btn-outline-danger btn-block"
                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this session?')) ?>>
                <?= FA::icon('times') ?> <?= Yii::t('app', 'Delete') ?>
            </a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="form-group">
            <?= Yii::t('app', 'Session') ?> <?= Yii::$app->formatter->asDatetime($session->clock_in) ?>
            <?= FA::icon('long-arrow-alt-right') ?>
            <?php if ($session->clock_out !== null): ?>
                <?= Yii::$app->formatter->asTime($session->clock_out) ?>
                <span class="badge badge-light float-right"><?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?></span>
            <?php else: ?>
                <?= Yii::t('app', 'not ended') ?>
            <?php endif; ?>
        </div>
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        <?= $form->field($model, 'startDate')->widget(DatePicker::class, ['date' => $model->getSession()->clock_in]) ?>
        <?= $form->field($model, 'endTime')->widget(MaskedInput::class, ['mask' => '99:99']) ?>
        <?= $form->field($model, 'projectId')->dropDownList($projects, ['class' => 'custom-select']) ?>
        <?= $form->field($model, 'note')->textarea() ?>

        <div class="form-group text-right">
            <?= Html::submitButton(
                FA::icon('check-circle') . ' ' . Yii::t('app', 'Save'),
                [
                    'class' => 'btn btn-primary btn-lg',
                    'name' => 'save-button',
                ]
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
