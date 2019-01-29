<?php

use app\widgets\fontawesome\FA;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\NewPasswordForm */

$this->title = Yii::t('app', 'New Password');

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
                <h3 class="card-title mb-5"><?= Yii::t('app', 'New Password') ?></h3>

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

                    <div class="row form-group field-newpasswordform-password required <?= $model->hasErrors('password') ? 'validating' : '' ?>">
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
                                        'id' => 'newpasswordform-password',
                                        'autofocus' => true,
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
    </div>
</div>
