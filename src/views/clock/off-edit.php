<?php

use app\widgets\confirm\Confirm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
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
    <div class="col-sm-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-primary btn-block"><i class="glyphicon glyphicon-backward"></i> <?= Yii::t('app', 'Go Back') ?></a>
        </div>
    </div>
    <div class="col-sm-10">
        <div class="form-group">
            <a href="<?= Url::to(['clock/off-delete', 'id' => $off->id]) ?>" class="btn btn-danger pull-right" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this off-time?')) ?>>
                <i class="glyphicon glyphicon-remove"></i> <?= Yii::t('app', 'delete') ?>
            </a>
            <?= Yii::t('app', 'Off-Time') ?> <?= Yii::$app->formatter->asDatetime($off->start_at, 'dd.MM.y') ?> <i class="glyphicon glyphicon-arrow-right"></i> <?= Yii::$app->formatter->asDatetime($off->end_at, 'dd.MM.y') ?>
        </div>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-4\">{input}</div>\n<div class=\"col-sm-6\">{error}</div>",
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
            ],
        ]); ?>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <p class="form-control-static"><?= Yii::t('app', 'Start Day') ?></p>
                </div>
            </div>

            <?= $form->field($model, 'startYear') ?>
            <?= $form->field($model, 'startMonth')->dropDownList(\app\models\Clock::months()) ?>
            <?= $form->field($model, 'startDay')->dropDownList(array_combine(range(1, 31), range(1, 31))) ?>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <p class="form-control-static"><?= Yii::t('app', 'End Day') ?></p>
                </div>
            </div>

            <?= $form->field($model, 'endYear') ?>
            <?= $form->field($model, 'endMonth')->dropDownList(\app\models\Clock::months()) ?>
            <?= $form->field($model, 'endDay')->dropDownList(array_combine(range(1, 31), range(1, 31))) ?>

            <div class="form-group">&nbsp;</div>

            <?= $form->field($model, 'note')->textarea() ?>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
