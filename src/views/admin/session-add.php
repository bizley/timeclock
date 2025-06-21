<?php

use app\models\Clock;
use app\models\ClockForm;
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
 * @var $users array
 */

$this->title = Yii::t('app', 'Adding Session');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Adding Session') ?></h1>
</div>

<div class="row">
    <div class="col-lg-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-outline-primary btn-block"><?= FA::icon('backward') ?>
                <?= Yii::t('app', 'Go Back') ?></a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="form-group">
            <?= Yii::t('app', 'New Session') ?>
        </div>

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        <?= $form->field($model, 'userId')->dropDownList($users, ['class' => 'custom-select']) ?>
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