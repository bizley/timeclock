<?php

/* @var $this yii\web\View */
/* @var $loginModel app\models\LoginForm */
/* @var $pinModel app\models\PinForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('app', 'Login');
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Login') ?></h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-1 control-label'],
    ],
]); ?>
    <?= $form->field($loginModel, 'email')->textInput(['autofocus' => true]) ?>
    <?= $form->field($loginModel, 'password')->passwordInput() ?>
    <?= $form->field($loginModel, 'rememberMe')->checkbox([
        'template' => "<div class=\"col-sm-offset-1 col-sm-3\">{input} {label}</div>\n<div class=\"col-sm-8\">{error}</div>",
    ]) ?>

    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <?= Html::submitButton(Yii::t('app', 'Log In'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            <?= Html::a(Yii::t('app', 'New Account'), ['site/register'], ['class' => 'btn']) ?>
            <?= Html::a(Yii::t('app', 'Reset Password'), ['site/reset'], ['class' => 'btn text-muted']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<hr>

<div class="form-group">
    <h1><?= Yii::t('app', 'or PIN') ?></h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-1 control-label'],
    ],
]); ?>
<?= $form->field($pinModel, 'pin') ?>
<?= $form->field($pinModel, 'rememberMe')->checkbox([
    'template' => "<div class=\"col-sm-offset-1 col-sm-3\">{input} {label}</div>\n<div class=\"col-sm-8\">{error}</div>",
]) ?>

    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <?= Html::submitButton(Yii::t('app', 'Log In'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>