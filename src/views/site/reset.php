<?php

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('app', 'Password Reset');
?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow">
            <div class="card-body">
                <div class="float-right mb-5">
                    <?= Html::a(
                        FA::icon('user') . ' ' . Yii::t('app', 'Login'),
                        ['site/login'],
                        ['class' => 'btn btn-outline-primary btn-sm']
                    ) ?>
                </div>
                <h3 class="card-title mb-5"><?= Yii::t('app', 'Password Reset') ?></h3>

                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                    <div class="form-group text-center">
                        <?= Html::submitButton(
                            FA::icon('redo-alt') . ' ' . Yii::t('app', 'Reset'),
                            [
                                'class' => 'btn btn-primary btn-lg',
                                'name' => 'reset-button',
                            ]
                        ) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
