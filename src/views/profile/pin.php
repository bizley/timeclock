<?php

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'PIN');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Your PIN is {pin}', [
            'pin' => \yii\bootstrap4\Html::tag('kbd', $pin),
        ]) ?></h1>
</div>
<div class="form-group">
    <h4>
        <?= Yii::t('app', 'You must remember the PIN since it will not be displayed anymore.') ?>
    </h4>
    <a href="<?= \yii\helpers\Url::to(['profile/index']) ?>" class="btn btn-default">
        <i class="glyphicon glyphicon-backward"></i>
        <?= Yii::t('app', 'Go Back') ?>
    </a>
</div>
