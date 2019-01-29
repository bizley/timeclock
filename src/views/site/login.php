<?php

/* @var $this yii\web\View */
/* @var $loginModel app\models\LoginForm */
/* @var $pinModel app\models\PinForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = Yii::t('app', 'Login');
?>
<div class="row align-items-end">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <div class="float-right mb-5">
                    <?= Html::a(Yii::t('app', 'New Account'), ['site/register'], ['class' => 'btn btn-outline-success btn-sm']) ?>
                    <?= Html::a(Yii::t('app', 'Reset Password'), ['site/reset'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                </div>
                <h3 class="card-title mb-5"><?= Yii::t('app', 'Login') ?></h3>
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'horizontalCssClasses' => [
                            'offset' => 'offset-sm-3',
                            'label' => 'col-sm-3 text-center',
                            'wrapper' => 'col-sm-9',
                        ],
                    ],
                ]); ?>

                    <?= $form->field($loginModel, 'email')->textInput(['autofocus' => true]) ?>
                    <?= $form->field($loginModel, 'password')->passwordInput() ?>
                    <?= $form->field($loginModel, 'rememberMe', [
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-9',
                        ],
                    ])->checkbox() ?>

                    <div class="form-group text-center">
                        <?= Html::submitButton(Yii::t('app', 'Log In'), [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'login-button',
                        ]) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mt-3">
        <div class="card shadow">
            <div class="card-body text-center">
                <h3 class="card-title mb-5"><?= Yii::t('app', 'or PIN') ?></h3>
                <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($pinModel, 'pin')->label(false) ?>
                    <?= $form->field($pinModel, 'rememberMe')->checkbox() ?>

                    <div class="form-group text-center">
                        <?= Html::submitButton(Yii::t('app', 'Log In'), [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'login-button',
                        ]) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
