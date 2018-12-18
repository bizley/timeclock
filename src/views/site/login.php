<?php

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Company Capital';
?>
<div class="form-group">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-1 control-label'],
    ],
]); ?>
    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"col-sm-offset-1 col-sm-3\">{input} {label}</div>\n<div class=\"col-sm-8\">{error}</div>",
    ]) ?>

    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <?= Html::submitButton('Zaloguj', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            <?= Html::a('Nowe konto', ['site/register'], ['class' => 'btn']) ?>
            <?= Html::a('Zresetuj hasło', ['site/reset'], ['class' => 'btn text-muted']) ?>
        </div>
    </div>

<?php ActiveForm::end();
