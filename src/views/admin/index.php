<?php

use app\models\User;
use app\widgets\confirm\Confirm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $users array */

$this->title = Yii::t('app', 'Employees');
?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Employees') ?></h1>
</div>

<div class="row">
    <div class="col-sm-12 table-responsive">
        <table class="table table-striped table-hover">
            <tr>
                <th><?= Yii::t('app', 'First And Last Name') ?></th>
                <th><?= Yii::t('app', 'Email') ?></th>
                <th><?= Yii::t('app', 'Phone Number') ?></th>
                <th><?= Yii::t('app', 'Role') ?></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= Html::encode($user->name) ?></td>
                <td><a href="mailto:<?= $user->email ?>"><?= $user->email ?></a></td>
                <td><?= Html::encode($user->phone) ?></td>
                <td>
                    <?php if ($user->role === User::ROLE_ADMIN): ?>
                        <span class="label label-primary"><?= Yii::t('app', 'ADMIN') ?></span>
                    <?php else: ?>
                        <span class="label label-default"><?= Yii::t('app', 'EMPLOYEE') ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($user->role === User::ROLE_ADMIN): ?>
                        <a href="<?= Url::to(['admin/demote', 'id' => $user->id]) ?>"
                           class="btn btn-default btn-xs"
                            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to remove admin access for this user?')) ?>>
                            <i class="glyphicon glyphicon-hand-down"></i> <?= Yii::t('app', 'set as employee') ?>
                        </a>
                    <?php else: ?>
                        <a href="<?= Url::to(['admin/promote', 'id' => $user->id]) ?>"
                           class="btn btn-primary btn-xs"
                            <?= Confirm::ask(Yii::t('app', 'Are you sure you want to promote this user for admin?')) ?>>
                            <i class="glyphicon glyphicon-hand-up"></i> <?= Yii::t('app', 'set as admin') ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <a href="<?= Url::to(['admin/reset', 'id' => $user->id]) ?>"
                       class="btn btn-warning btn-xs"
                        <?= Confirm::ask(Yii::t('app', 'Are you sure you want to send password reset link?')) ?>>
                        <i class="glyphicon glyphicon-flash"></i> <?= Yii::t('app', 'send password reset link') ?>
                    </a>
                </td>
                <td class="text-right">
                    <a href="<?= Url::to(['admin/delete', 'id' => $user->id]) ?>"
                       class="btn btn-danger btn-xs"
                        <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this user?')) ?>>
                        <i class="glyphicon glyphicon-remove"></i> <?= Yii::t('app', 'delete') ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
