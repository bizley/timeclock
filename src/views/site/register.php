<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\RegisterForm */

use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('app', 'New Account');

$this->registerJs(<<<JS
$(".password").click(function () {
    let icon = $(this);
    if (icon.hasClass("fa-eye")) {
        icon.removeClass("fa-eye").addClass("fa-eye-slash").closest(".input-group").find("input[type=password]").attr("type", "text");
    } else {
        icon.removeClass("fa-eye-slash").addClass("fa-eye").closest(".input-group").find("input[type=text]").attr("type", "password");
    }
});
JS
);
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
                <h3 class="card-title mb-5"><?= Yii::t('app', 'New Account') ?></h3>

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
                    <div class="row form-group field-registerform-emailaccount field-registerform-emaildomain required <?= $model->hasErrors('emailAccount') || $model->hasErrors('emailDomain') ? 'validating' : '' ?>">
                        <?= Html::activeLabel(
                            $model,
                            'emailAccount',
                            ['class' => 'col-sm-3 text-center']
                        ) ?>
                        <div class="col-sm-9 email">
                            <?= Html::activeDropDownList(
                                $model,
                                'emailDomain',
                                array_combine(Yii::$app->params['allowedDomains'], Yii::$app->params['allowedDomains']),
                                [
                                    'class' => 'form-control custom-select float-right ' . ($model->hasErrors('emailDomain') ? 'is-invalid' : ''),
                                    'id' => 'registerform-emaildomain',
                                ]
                            ) . Html::activeTextInput(
                                $model,
                                'emailAccount',
                                [
                                    'class' => 'form-control ' . ($model->hasErrors('emailAccount') ? 'is-invalid' : ''),
                                    'id' => 'registerform-emailaccount',
                                    'autofocus' => true,
                                ]
                            ) ?>
                            <?= Html::error($model, 'emailAccount') ?>
                            <?= Html::error($model, 'emailDomain') ?>
                        </div>
                    </div>

                    <div class="row form-group field-registerform-name required <?= $model->hasErrors('name') ? 'validating' : '' ?>">
                        <?= Html::activeLabel(
                            $model,
                            'name',
                            ['class' => 'col-sm-3 text-center']
                        ) ?>
                        <div class="col-sm-9">
                            <?= Html::activeTextInput(
                                $model,
                                'name',
                                [
                                    'class' => 'form-control ' . ($model->hasErrors('name') ? 'is-invalid' : ''),
                                    'id' => 'registerform-name',
                                ]
                            ) ?>
                            <?= Html::error($model, 'name') ?>
                        </div>
                    </div>

                    <div class="row form-group field-registerform-password required <?= $model->hasErrors('password') ? 'validating' : '' ?>">
                        <?= Html::activeLabel(
                            $model,
                            'password',
                            ['class' => 'col-sm-3 text-center']
                        ) ?>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <?= Html::activePasswordInput(
                                    $model,
                                    'password',
                                    [
                                        'class' => 'form-control ' . ($model->hasErrors('password') ? 'is-invalid' : ''),
                                        'id' => 'registerform-password',
                                    ]
                                ) ?>
                                <div class="input-group-append">
                                    <div class="input-group-text"><?= FA::icon('eye', ['options' => ['class' => 'password']]) ?></div>
                                </div>
                                <?= Html::error($model, 'password') ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <?= Html::submitButton(
                            FA::icon('user-circle') . ' ' . Yii::t('app', 'Register'),
                            [
                                'class' => 'btn btn-primary btn-lg',
                                'name' => 'register-button',
                            ]
                        ) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
