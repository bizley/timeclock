<?php

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Company Capital | Reset hasła';
?>
<div class="form-group">
    <h1>Reset hasła</h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-1 control-label'],
    ],
]); ?>
    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <?= Html::submitButton('Resetuj', ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
        </div>
    </div>

<?php ActiveForm::end();
