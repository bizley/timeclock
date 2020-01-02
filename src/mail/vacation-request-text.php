<?= Yii::t('app', 'User {user} requested vacation from {start} to {end}, this awaits administrator approval.', [
    'user' => $user,
    'start' => $start,
    'end' => $end,
]) ?>

<?= Yii::t('app', 'Go to the admin panel') ?>: <?= $link ?>

