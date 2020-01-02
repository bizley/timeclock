<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use app\widgets\fontawesome\FA;
use yii\helpers\Html;

$this->title = $name;
?>
<div class="row">
    <div class="col-lg-6">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>
        <p>The above error occurred while the Web server was processing your request.</p>
        <p>Please contact us if you think this is a server error. Thank you.</p>
        <?php if (Yii::$app->user && Yii::$app->user->isGuest): ?>
            <?= Html::a(
                FA::icon('user') . ' ' . Yii::t('app', 'Login'),
                ['site/login'],
                ['class' => 'btn btn-primary']
            ) ?>
        <?php endif; ?>
    </div>
</div>
