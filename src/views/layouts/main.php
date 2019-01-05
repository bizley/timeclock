<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\models\User;
use app\widgets\alert\Alert;
use app\widgets\confirm\Confirm;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->params['company'] . ' | ' . Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="bg"><i class="glyphicon glyphicon-time"></i></div>

<div id="main">
    <div class="wrap">
        <div class="container">
            <?= Alert::widget() ?>
            <?php if (!Yii::$app->user->isGuest): ?>
                <ul class="pull-right list-inline menu">
                    <li><a href="<?= Url::to(['profile/index']) ?>"><i class="glyphicon glyphicon-user"></i> <?= Html::encode(Yii::$app->user->identity->name) ?></a></li>
                    <li><a href="<?= Url::to(['site/index']) ?>"><i class="glyphicon glyphicon-play"></i> <?= Yii::t('app', 'Current Session') ?></a></li>
                    <li><a href="<?= Url::to(['clock/add']) ?>"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('app', 'Add Session') ?></a></li>
                    <li><a href="<?= Url::to(['clock/off-add']) ?>"><i class="glyphicon glyphicon-plus-sign"></i> <?= Yii::t('app', 'Add Off-Time') ?></a></li>
                    <li><a href="<?= Url::to(['clock/history']) ?>"><i class="glyphicon glyphicon-list"></i> <?= Yii::t('app', 'History') ?></a></li>
                    <li><a href="<?= Url::to(['clock/calendar']) ?>"><i class="glyphicon glyphicon-calendar"></i> <?= Yii::t('app', 'Calendar') ?></a></li>
                    <li><a href="<?= Url::to(['site/logout']) ?>" data-method="post"><i class="glyphicon glyphicon-log-out"></i> <?= Yii::t('app', 'Log Out') ?></a></li>
                </ul>
                <?php if (Yii::$app->user->identity->role === User::ROLE_ADMIN): ?>
                    <div class="clearfix"></div>
                    <ul class="pull-right list-inline menu-admin">
                        <li><a href="<?= Url::to(['admin/index']) ?>"><i class="glyphicon glyphicon-user"></i> <?= Yii::t('app', 'Employees') ?></a></li>
                        <li><a href="<?= Url::to(['admin/history']) ?>"><i class="glyphicon glyphicon-list"></i> <?= Yii::t('app', 'Sessions') ?></a></li>
                        <li><a href="<?= Url::to(['admin/calendar']) ?>"><i class="glyphicon glyphicon-calendar"></i> <?= Yii::t('app', 'Overall Calendar') ?></a></li>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <?php if (!Yii::$app->user->isGuest): ?>
                <p class="pull-right">
                    <?php if (Yii::$app->user->identity->theme === 'light'): ?>
                        <a href="<?= Url::to(['profile/dark']) ?>" class="text-muted"><?= Yii::t('app', 'Switch to dark theme') ?></a>
                    <?php else: ?>
                        <a href="<?= Url::to(['profile/light']) ?>" class="text-muted"><?= Yii::t('app', 'Switch to light theme') ?></a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <p>&copy; <?= Yii::$app->params['company'] ?> <?= date('Y') ?></p>
        </div>
    </footer>
</div>

<?= Confirm::widget() ?>

<?php if (array_key_exists('dayModal', $this->params)): ?>
<div class="modal fade" id="dayModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-day"></div>
    </div>
</div>
<?php endif; ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
