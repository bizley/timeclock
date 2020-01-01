<p><?= Yii::t('app', 'User {user} requested vacation from {start} to {end}, this awaits administrator approval.', [
    'user' => $user,
    'start' => $start,
    'end' => $end,
]) ?></p>
<p><a href="<?= $link ?>"><?= Yii::t('app', 'Go to the admin panel') ?></a></p>