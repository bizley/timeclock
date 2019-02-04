<?php

use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */

$this->title = Yii::t('app', 'PIN');
?>
<div class="form-group pt-5">
    <div class="card">
        <div class="card-body">
            <h1 class="text-dark">
                <?= FA::icon('th') ?>
                <?= Yii::t('app', 'Your PIN is {pin}', ['pin' => Html::tag('kbd', $pin)]) ?>
            </h1>
        </div>
    </div>
</div>
<div class="form-group">
    <h4><?= FA::icon('exclamation-triangle') ?> <?= Yii::t('app', 'You must remember the PIN since it will not be displayed anymore.') ?></h4>
</div>
<div class="form-group">
    <a href="<?= Url::to(['profile/index']) ?>" class="btn btn-primary btn-lg">
        <?= FA::icon('backward') ?>
        <?= Yii::t('app', 'Go Back') ?>
    </a>
</div>