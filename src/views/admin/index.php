<?php

use app\models\User;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $users array */

$this->title = 'Company Timeclock | Pracownicy';
?>
<div class="form-group">
    <h1>Pracownicy</h1>
</div>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <ul class="list-group">
            <?php foreach ($users as $user): ?>
                <li class="list-group-item">
                    <p class="pull-right">
                        <?php if ($user->role === User::ROLE_ADMIN): ?>
                            <a href="<?= Url::to(['admin/demote', 'id' => $user->id]) ?>"
                               class="btn btn-default btn-xs"
                               data-confirm="Czy na pewno chcesz pozbawić użytkownika dostępu admina?"
                               data-method="post"><i class="glyphicon glyphicon-hand-down"></i> ustaw jako pracownik</a>
                        <?php else: ?>
                            <a href="<?= Url::to(['admin/promote', 'id' => $user->id]) ?>"
                               class="btn btn-primary btn-xs"
                               data-confirm="Czy na pewno chcesz awansować użytkownika na admina?"
                               data-method="post"><i class="glyphicon glyphicon-hand-up"></i> ustaw jako admin</a>
                        <?php endif; ?>
                        <a href="<?= Url::to(['admin/reset', 'id' => $user->id]) ?>"
                           class="btn btn-warning btn-xs"
                           data-confirm="Czy na pewno chcesz wysłać link resetujący hasło?"
                           data-method="post"><i class="glyphicon glyphicon-flash"></i> wyślij link resetujący hasło</a>
                        <a href="<?= Url::to(['admin/delete', 'id' => $user->id]) ?>"
                           class="btn btn-danger btn-xs"
                           data-confirm="Czy na pewno chcesz usunąć użytkownika?"
                           data-method="post"><i class="glyphicon glyphicon-remove"></i> usuń</a>
                    </p>
                    <?= Html::encode($user->name) ?>
                    <a href="mailto:<?= $user->email ?>"><?= $user->email ?></a>
                    <?php if ($user->role === User::ROLE_ADMIN): ?>
                        <span class="label label-primary">ADMIN</span>
                    <?php else: ?>
                        <span class="label label-default">PRACOWNIK</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
