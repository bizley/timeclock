<?php

use app\widgets\confirm\Confirm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\ProfileForm */

$this->title = Yii::t('app', 'Profile');

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
    <h1><?= Html::encode(Yii::$app->user->identity->name) ?></h1>
</div>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-7\">{error}</div>",
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
    ],
]); ?>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= Yii::t('app', 'Email') ?></label>
        <div class="col-sm-3">
            <p class="form-control-static"><?= Yii::$app->user->identity->email ?></p>
        </div>
    </div>

    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'phone') ?>

    <div class="form-group field-profileform-password <?= $model->hasErrors('password') ? 'has-error' : '' ?>">
        <?= Html::activeLabel($model, 'password', ['class' => 'col-sm-2 control-label']) ?>
        <div class="col-sm-3">
            <div class="input-group">
                <?= Html::activePasswordInput($model, 'password', ['class' => 'form-control', 'id' => 'profileform-password']) ?>
                <div class="input-group-addon"><i class="glyphicon glyphicon-eye-open password"></i></div>
            </div>
        </div>
        <div class="col-sm-6"><?= Html::error($model, 'password', ['class' => 'help-block help-block-error']) ?></div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<hr>

<div class="form-group">
    <h3><?= Yii::t('app', 'PIN') ?></h3>
</div>
<div class="form-group">
    <h4>
        <?= Yii::t('app', 'You can sign in using PIN as well.') ?>
        <a href="<?= Url::to(['profile/pin']) ?>" class="btn btn-success" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to generate new PIN?')) ?>>
            <i class="glyphicon glyphicon-th"></i> <?= Yii::t('app', 'Generate PIN') ?>
        </a>
    </h4>
</div>

<hr>

<div class="form-group">
    <h3><?= Yii::t('app', 'API Access') ?></h3>
</div>
<div class="form-group">
    <?php if (empty(Yii::$app->user->identity->api_key)): ?>
        <p>
            <?= Yii::t('app', 'You currently don\'t have API access.') ?>
            <a href="<?= Url::to(['profile/grant']) ?>" data-method="post" class="btn btn-sm btn-primary">
                <i class="glyphicon glyphicon-flash"></i>
                <?= Yii::t('app', 'Grant yourself API access') ?>
            </a>
        </p>
    <?php else: ?>
        <div class="pull-right">
            <a href="<?= Url::to(['profile/change']) ?>" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to change API key?')) ?> class="btn btn-xs btn-warning">
                <i class="glyphicon glyphicon-flash"></i>
                <?= Yii::t('app', 'Change API key') ?>
            </a>
            <a href="<?= Url::to(['profile/revoke']) ?>" <?= Confirm::ask(Yii::t('app', 'Are you sure you want to revoke API access?')) ?> class="btn btn-xs btn-danger">
                <i class="glyphicon glyphicon-off"></i>
                <?= Yii::t('app', 'Revoke API access') ?>
            </a>
        </div>
        <h4>
            <?= Yii::t('app', 'Your API identifier is {id} and your access key is {key}.', [
                'id' => Html::tag('kbd', Yii::$app->user->id),
                'key' => Html::tag('kbd', Yii::$app->user->identity->api_key),
            ]) ?>
        </h4>
        <a href="<?= Url::to(['profile/api']) ?>" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('app', 'How to use API?') ?></a>
    <?php endif; ?>
</div>