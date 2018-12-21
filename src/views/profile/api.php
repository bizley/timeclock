<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \app\models\ProfileForm */

$this->title = Yii::t('app', 'API');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'How to use API?') ?></h1>
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
        <p class="pull-right">
            <a href="<?= Url::to(['profile/change']) ?>" data-method="post" data-confirm="<?= Yii::t('app', 'Are you sure you want to change API key?') ?>" class="btn btn-sm btn-warning">
                <i class="glyphicon glyphicon-flash"></i>
                <?= Yii::t('app', 'Change API key') ?>
            </a>
            <a href="<?= Url::to(['profile/revoke']) ?>" data-method="post" data-confirm="<?= Yii::t('app', 'Are you sure you want to revoke API access?') ?>" class="btn btn-sm btn-danger">
                <i class="glyphicon glyphicon-off"></i>
                <?= Yii::t('app', 'Revoke API access') ?>
            </a>
        </p>
        <p><?= Yii::t('app', 'Your API identifier is {id} and your access key is {key}.', [
                'id' => Html::tag('kbd', Yii::$app->user->id),
                'key' => Html::tag('kbd', Yii::$app->user->identity->api_key),
            ]) ?></p>
    <?php endif; ?>
</div>