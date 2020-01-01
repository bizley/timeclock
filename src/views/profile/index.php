<?php

use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\ProfileForm */
/* @var $projects array */

$this->title = Yii::t('app', 'Profile');

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
<div class="form-group mt-5">
    <h1><?= FA::icon('user') ?> <?= Html::encode(Yii::$app->user->identity->name) ?></h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'offset' => 'offset-sm-3',
            'label' => 'col-lg-3 col-md-4 text-center',
            'wrapper' => 'col-lg-4 col-md-6',
        ],
    ],
]); ?>
    <div class="row form-group">
        <label class="col-lg-3 col-md-4 text-center"><?= Yii::t('app', 'Email') ?></label>
        <div class="col-lg-4 col-md-6">
            <p class="text-center text-md-left"><?= Yii::$app->user->identity->email ?></p>
        </div>
    </div>

    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'phone') ?>

    <div class="row form-group field-profileform-password <?= $model->hasErrors('password') ? 'validating' : '' ?>">
        <?= Html::activeLabel($model, 'password', ['class' => 'col-lg-3 col-md-4 text-center']) ?>
        <div class="col-lg-4 col-md-6">
            <div class="input-group">
                <?= Html::activePasswordInput(
                    $model,
                    'password',
                    [
                        'class' => 'form-control ' . ($model->hasErrors('password') ? 'is-invalid' : ''),
                        'id' => 'profileform-password',
                    ]
                ) ?>
                <div class="input-group-append">
                    <div class="input-group-text"><?= FA::icon('eye', ['options' => ['class' => 'password']]) ?></div>
                </div>
                <?= Html::error($model, 'password') ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'projectId')->dropDownList($projects, ['class' => 'custom-select']) ?>

    <div class="row form-group">
        <div class="offset-lg-3 offset-md-4 col text-center text-md-left">
            <?= Html::submitButton(
                FA::icon('check-circle') . ' ' . Yii::t('app', 'Save'),
                [
                    'class' => 'btn btn-primary btn-lg',
                    'name' => 'save-button',
                ]
            ) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<hr>

<div class="form-group">
    <h3><?= FA::icon('th') ?> <?= Yii::t('app', 'PIN') ?></h3>
</div>
<div class="form-group">
    <h4>
        <?= Yii::t('app', 'You can sign in using PIN as well.') ?>
        <a href="<?= Url::to(['profile/pin']) ?>" class="btn btn-success" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to generate new PIN?')) ?>>
            <?= FA::icon('th') ?> <?= Yii::t('app', 'Generate PIN') ?>
        </a>
    </h4>
</div>

<hr>

<div class="form-group">
    <h3><?= FA::icon('cloud') ?> <?= Yii::t('app', 'API Access') ?></h3>
</div>
<div class="form-group">
    <?php if (empty(Yii::$app->user->identity->api_key)): ?>
        <p>
            <?= Yii::t('app', 'You currently don\'t have API access.') ?>
            <a href="<?= Url::to(['profile/grant']) ?>" data-method="post" class="btn btn-sm btn-primary">
                <?= FA::icon('cloud') ?>
                <?= Yii::t('app', 'Grant yourself API access') ?>
            </a>
        </p>
    <?php else: ?>
        <div class="float-sm-right ml-1 mb-3">
            <a href="<?= Url::to(['profile/change']) ?>"
                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to change API key?')) ?>
               class="btn btn-sm btn-warning mb-1">
                <?= FA::icon('redo-alt') ?>
                <?= Yii::t('app', 'Change API key') ?>
            </a>
            <a href="<?= Url::to(['profile/revoke']) ?>"
                <?= Confirm::ask(Yii::t('app', 'Are you sure you want to revoke API access?')) ?>
               class="btn btn-sm btn-danger mb-1">
                <?= FA::icon('power-off') ?>
                <?= Yii::t('app', 'Revoke API access') ?>
            </a>
        </div>
        <h4>
            <?= Yii::t('app', 'Your API identifier is {id} and your access key is {key}.', [
                'id' => Html::tag('kbd', Yii::$app->user->id),
                'key' => Html::tag('kbd', Yii::$app->user->identity->api_key),
            ]) ?>
        </h4>
        <a href="<?= Url::to(['profile/api']) ?>" class="btn btn-outline-primary">
            <?= FA::icon('info-circle') ?> <?= Yii::t('app', 'How to use API?') ?>
        </a>
    <?php endif; ?>
</div>