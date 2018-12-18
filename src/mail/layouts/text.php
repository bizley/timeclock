<?php

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

$this->beginPage();
$this->beginBody(); ?>
<?= $content ?>

--
<?= date('Y') ?> copyright <?= Yii::$app->params['company'] ?>
<?php $this->endBody();
$this->endPage();
