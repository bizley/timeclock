<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\NewPasswordForm */

$this->title = 'Company Timeclock | Nowe hasło';

$this->registerJs(<<<JS
$(".password").click(function () {
    let icon = $(this);
    if (icon.hasClass("glyphicon-eye-open")) {
        icon.removeClass("glyphicon-eye-open").addClass("glyphicon-eye-close").closest(".input-group").find("input[type=password]").attr("type", "text");
    } else {
        icon.removeClass("glyphicon-eye-close").addClass("glyphicon-eye-open").closest(".input-group").find("input[type=text]").attr("type", "password");
    }
});
JS
);
?>
<div class="form-group">
    <h1>Nowe hasło</h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-7\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
    ],
]); ?>
    <div class="form-group field-newpasswordform-password <?= $model->hasErrors('password') ? 'has-error' : '' ?>">
        <?= Html::activeLabel($model, 'password', ['class' => 'col-sm-2 control-label']) ?>
        <div class="col-sm-3">
            <div class="input-group">
                <?= Html::activePasswordInput($model, 'password', ['class' => 'form-control', 'id' => 'newpasswordform-password', 'autofocus' => true]) ?>
                <div class="input-group-addon"><i class="glyphicon glyphicon-eye-open password"></i></div>
            </div>
        </div>
        <div class="col-sm-6"><?= Html::error($model, 'password', ['class' => 'help-block help-block-error']) ?></div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
        </div>
    </div>

<?php ActiveForm::end();
