<?php

use app\models\Clock;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\OffForm */
/* @var $off \app\models\Off */

$this->title = Yii::t('app', 'Editing Off-Time');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Editing Off-Time') ?></h1>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-outline-primary btn-block"><?= FA::icon('backward') ?> <?= Yii::t('app', 'Go Back') ?></a>
        </div>
        <div class="form-group">
            <a href="<?= Url::to(['clock/off-delete', 'id' => $off->id]) ?>"
               class="btn btn-outline-danger btn-block"
                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this off-time?')) ?>>
                <?= FA::icon('times') ?> <?= Yii::t('app', 'Delete') ?>
            </a>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <?= Yii::t('app', 'Off-Time') ?>
            <?= Yii::$app->formatter->asDate($off->start_at) ?>
            <?= FA::icon('long-arrow-alt-right') ?>
            <?= Yii::$app->formatter->asDate($off->end_at) ?>
        </div>
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

            <div class="row form-group">
                <div class="col offset-sm-2">
                    <p><?= Yii::t('app', 'Start Day') ?></p>
                </div>
            </div>

            <?= $form->field($model, 'startYear') ?>
            <?= $form->field($model, 'startMonth')->dropDownList(Clock::months(), ['class' => 'form-control custom-select']) ?>
            <?= $form->field($model, 'startDay')->dropDownList(
                array_combine(range(1, 31), range(1, 31)),
                ['class' => 'form-control custom-select']
            ) ?>

            <div class="row form-group">
                <div class="col offset-sm-2">
                    <p><?= Yii::t('app', 'End Day') ?></p>
                </div>
            </div>

            <?= $form->field($model, 'endYear') ?>
            <?= $form->field($model, 'endMonth')->dropDownList(Clock::months(), ['class' => 'form-control custom-select']) ?>
            <?= $form->field($model, 'endDay')->dropDownList(
                array_combine(range(1, 31), range(1, 31)),
                ['class' => 'form-control custom-select']
            ) ?>

            <div class="form-group">&nbsp;</div>

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
