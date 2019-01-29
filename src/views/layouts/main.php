<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\models\User;
use app\widgets\alert\Alert;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use app\widgets\modal\Clock;
use app\widgets\modal\Day;
use app\widgets\theme\Theme;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->params['company'] . ' | ' . Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="bg"><?= FA::icon('clock', ['style' => 'far']) ?></div>

<div id="main">
    <div class="wrap">
        <div class="container">
            <?= Alert::widget() ?>
            <?php if (!Yii::$app->user->isGuest): ?>
                <ul class="float-right list-inline menu">
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['profile/index']) ?>"><?= FA::icon('user') ?> <?= Html::encode(Yii::$app->user->identity->name) ?></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['site/index']) ?>"><?= FA::icon('play-circle') ?> <?= Yii::t('app', 'Current Session') ?></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['clock/add']) ?>"><?= FA::icon('plus') ?> <?= Yii::t('app', 'Add Session') ?></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['clock/off-add']) ?>"><?= FA::icon('plus-circle') ?> <?= Yii::t('app', 'Add Off-Time') ?></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['clock/history']) ?>"><?= FA::icon('history') ?> <?= Yii::t('app', 'History') ?></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['clock/calendar']) ?>"><?= FA::icon('calendar-alt') ?> <?= Yii::t('app', 'Calendar') ?></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="<?= Url::to(['site/logout']) ?>" data-method="post"><?= FA::icon('sign-out-alt') ?> <?= Yii::t('app', 'Log Out') ?></a>
                    </li>
                </ul>
                <?php if (Yii::$app->user->identity->role === User::ROLE_ADMIN): ?>
                    <div class="clearfix"></div>
                    <ul class="float-right list-inline menu-admin">
                        <li class="list-inline-item">
                            <a href="<?= Url::to(['admin/index']) ?>"><?= FA::icon('users') ?> <?= Yii::t('app', 'Employees') ?></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="<?= Url::to(['admin/history']) ?>"><?= FA::icon('list-alt') ?> <?= Yii::t('app', 'Sessions') ?></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="<?= Url::to(['admin/calendar']) ?>"><?= FA::icon('calendar-alt') ?> <?= Yii::t('app', 'Overall Calendar') ?></a>
                        </li>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <?= Theme::widget() ?>
            <p>&copy; <?= Yii::$app->params['company'] ?> <?= date('Y') ?></p>
        </div>
    </footer>
</div>

<?= Confirm::widget() ?>
<?= Day::widget(['params' => $this->params]) ?>
<?= Clock::widget(['params' => $this->params]) ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
