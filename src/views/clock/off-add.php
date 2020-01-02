<?php

use app\models\OffForm;
use app\widgets\date\DatePicker;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model OffForm
 * @var $marked array
 */

$this->title = Yii::t('app', 'Adding Off-Time');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Adding Off-Time') ?></h1>
</div>

<div class="row">
    <div class="col-lg-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-outline-primary btn-block"><?= FA::icon('backward') ?> <?= Yii::t('app', 'Go Back') ?></a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="form-group">
            <?= Yii::t('app', 'New Off-Time') ?>
        </div>
        <?php $form = ActiveForm::begin(); ?>

            <div class="form-group row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'startDate')->widget(DatePicker::class, [
                        'date' => $model->getOff()->start_at,
                        'timePicker' => false,
                        'showOtherMonths' => false,
                        'marked' => $marked,
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'endDate')->widget(DatePicker::class, [
                        'date' => $model->getOff()->end_at,
                        'timePicker' => false,
                        'showOtherMonths' => false,
                        'marked' => $marked,
                    ]) ?>
                </div>
            </div>

            <?= $form
                ->field(
                    $model,
                    'type',
                    ['checkTemplate' => "<div class=\"custom-control custom-checkbox\">\n{input}\n{label}\n{error}\n{hint}\n</div>"]
                )
                ->checkbox(['class' => 'custom-control-input'])
                ->label(
                    Yii::t('app', 'Vacation'),
                    ['class' => 'custom-control-label']
                )
                ->hint(Yii::t('app', 'If you mark it as vacation administrator will get new notification and will have to approve it to make it official.')) ?>

            <?= $form->field($model, 'note')->textarea() ?>

            <div class="form-group text-right">
                <?= Html::submitButton(
                    FA::icon('check-circle') . ' ' . Yii::t('app', 'Save'),
                    [
                        'class' => 'btn btn-primary',
                        'name' => 'save-button',
                    ]
                ) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
