<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $session \app\models\Clock */
/* @var $model \app\models\ClockForm */

$this->title = 'Company Timeclock | Zmiana sesji';

$minutes = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55];
?>
<div class="form-group">
    <h1>Zmiana sesji</h1>
</div>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group">
            <a href="<?= Url::previous() ?>" class="btn btn-primary btn-block"><i class="glyphicon glyphicon-backward"></i> Powrót</a>
        </div>
    </div>
    <div class="col-sm-10">
        <div class="form-group">
            <a href="<?= Url::to(['clock/delete', 'id' => $session->id]) ?>" class="btn btn-danger pull-right" data-confirm="Czy na pewno chcesz usunąć tę sesję?" data-method="post">
                <i class="glyphicon glyphicon-remove"></i> usuń
            </a>
            Sesja <?= Yii::$app->formatter->asDatetime($session->clock_in) ?>
            <i class="glyphicon glyphicon-arrow-right"></i>
            <?php if ($session->clock_out !== null): ?>
                <?= Yii::$app->formatter->asTime($session->clock_out) ?>
                <span class="badge"><?= Yii::$app->formatter->asDuration($session->clock_out - $session->clock_in) ?></span>
                <?php else: ?>
                niezamknięta
            <?php endif; ?>
        </div>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-4\">{input}</div>\n<div class=\"col-sm-6\">{error}</div>",
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
            ],
        ]); ?>
        <?= $form->field($model, 'year') ?>
        <?= $form->field($model, 'month')->dropDownList(\app\models\Clock::months()) ?>
        <?= $form->field($model, 'day')->dropDownList(array_combine(range(1, 31), range(1, 31))) ?>

        <div class="form-group field-clockform-starthour field-clockform-startminute required <?= $model->hasErrors('startHour') || $model->hasErrors('startMinute') ? 'has-error' : '' ?>">
            <?= Html::activeLabel($model, 'startHour', ['class' => 'col-sm-2 control-label']) ?>
            <div class="col-sm-2">
                <?= Html::activeDropDownList(
                        $model,
                        'startHour',
                        array_combine(range(0, 23), range(0, 23)),
                        ['class' => 'form-control', 'id' => 'clockform-starthour']
                ) ?>
            </div>
            <div class="col-sm-2">
                <?= Html::activeDropDownList(
                    $model,
                    'startMinute',
                    array_combine($minutes, $minutes),
                    ['class' => 'form-control', 'id' => 'clockform-startminute']
                ) ?>
            </div>
            <div class="col-sm-6">
                <?= Html::error($model, 'startHour', ['class' => 'help-block help-block-error']) ?>
                <?= Html::error($model, 'startMinute', ['class' => 'help-block help-block-error']) ?>
            </div>
        </div>

        <div class="form-group field-clockform-endhour field-clockform-endminute <?= $model->hasErrors('endHour') || $model->hasErrors('endMinute') ? 'has-error' : '' ?>">
            <?= Html::activeLabel($model, 'endHour', ['class' => 'col-sm-2 control-label']) ?>
            <div class="col-sm-2">
                <?= Html::activeDropDownList(
                    $model,
                    'endHour',
                    ['' => ''] + array_combine(range(0, 23), range(0, 23)),
                    ['class' => 'form-control', 'id' => 'clockform-endhour']
                ) ?>
            </div>
            <div class="col-sm-2">
                <?= Html::activeDropDownList(
                    $model,
                    'endMinute',
                    ['' => ''] + array_combine($minutes, $minutes),
                    ['class' => 'form-control', 'id' => 'clockform-endminute']
                ) ?>
            </div>
            <div class="col-sm-6">
                <?= Html::error($model, 'endHour', ['class' => 'help-block help-block-error']) ?>
                <?= Html::error($model, 'endMinute', ['class' => 'help-block help-block-error']) ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
