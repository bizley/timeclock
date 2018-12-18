<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegisterForm */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'New Account');

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
    <h1><?= Yii::t('app', 'New Account') ?></h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-4\">{input}</div>\n<div class=\"col-sm-6\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
    ],
]); ?>
    <div class="form-group field-registerform-emailaccount field-registerform-emaildomain required <?= $model->hasErrors('emailAccount') || $model->hasErrors('emailDomain') ? 'has-error' : '' ?>">
        <?= Html::activeLabel($model, 'emailAccount', ['class' => 'col-sm-2 control-label']) ?>
        <div class="col-sm-4 email">
            <?= Html::activeDropDownList(
                    $model,
                    'emailDomain',
                    array_combine(Yii::$app->params['allowedDomains'], Yii::$app->params['allowedDomains']),
                    ['class' => 'form-control pull-right', 'id' => 'registerform-emaildomain']
            ) ?>
            <?= Html::activeTextInput($model, 'emailAccount', ['class' => 'form-control', 'id' => 'registerform-emailaccount', 'autofocus' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= Html::error($model, 'emailAccount', ['class' => 'help-block help-block-error']) ?>
            <?= Html::error($model, 'emailDomain', ['class' => 'help-block help-block-error']) ?>
        </div>
    </div>

    <div class="form-group field-registerform-name required <?= $model->hasErrors('name') ? 'has-error' : '' ?>">
        <?= Html::activeLabel($model, 'name', ['class' => 'col-sm-2 control-label']) ?>
        <div class="col-sm-4">
            <?= Html::activeTextInput($model, 'name', ['class' => 'form-control', 'id' => 'registerform-name']) ?>
        </div>
        <div class="col-sm-6"><?= Html::error($model, 'name', ['class' => 'help-block help-block-error']) ?></div>
    </div>

    <div class="form-group field-registerform-password required <?= $model->hasErrors('password') ? 'has-error' : '' ?>">
        <?= Html::activeLabel($model, 'password', ['class' => 'col-sm-2 control-label']) ?>
        <div class="col-sm-4">
            <div class="input-group">
                <?= Html::activePasswordInput($model, 'password', ['class' => 'form-control', 'id' => 'registerform-password']) ?>
                <div class="input-group-addon"><i class="glyphicon glyphicon-eye-open password"></i></div>
            </div>
        </div>
        <div class="col-sm-6"><?= Html::error($model, 'password', ['class' => 'help-block help-block-error']) ?></div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-11">
            <?= Html::submitButton(Yii::t('app', 'Register'), ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
        </div>
    </div>

<?php ActiveForm::end();
