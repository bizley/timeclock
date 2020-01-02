<?= Yii::t('app', 'Hello, {user}', ['user' => $user]) ?>

<?= Yii::t('app', 'Your vacation request ({start} - {end}) has been denied :(', [
    'start' => $start,
    'end' => $end,
]) ?>
