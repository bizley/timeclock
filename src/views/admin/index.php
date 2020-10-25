<?php

use app\models\Terminal;
use app\models\User;
use app\widgets\confirm\Confirm;
use app\widgets\fontawesome\FA;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $user User
 * @var $users array
 */

$this->title = Yii::t('app', 'Employees');

?>
<div class="form-group">
    <h1><?= Yii::t('app', 'Employees') ?></h1>
</div>

<div class="row">
    <div class="col-sm-12 table-responsive">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col"><?= Yii::t('app', 'First And Last Name') ?></th>
                <th scope="col">
                    <?= Yii::t('app', 'Email') ?> /<br>
                    <?= Yii::t('app', 'Phone Number') ?>
                </th>
                <th scope="col"><?= Yii::t('app', 'Role') ?></th>
                <th scope="col"><?= Yii::t('app', 'Status') ?></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= $user->status === User::STATUS_DELETED ? '<del>' : '' ?>
                        <?= Html::encode($user->name) ?>
                        <?= $user->status === User::STATUS_DELETED ? '</del>' : '' ?>
                    </td>
                    <td>
                        <?= $user->status === User::STATUS_DELETED ? '<del>' : '' ?>
                            <a href="mailto:<?= $user->email ?>"><?= $user->email ?></a><br>
                            <?= Html::encode($user->phone) ?>
                        <?= $user->status === User::STATUS_DELETED ? '</del>' : '' ?>
                    </td>
                    <td>
                        <?php if ($user->role === User::ROLE_ADMIN): ?>
                            <span class="badge badge-primary"><?= Yii::t('app', 'ADMIN') ?></span>
                            <a href="<?= Url::to(['admin/demote', 'id' => $user->id]) ?>"
                               class="badge badge-warning"
                               <?= Confirm::ask(Yii::t('app', 'Are you sure you want to remove admin access for this user?')) ?>>
                                <?= FA::icon('hand-point-down') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'set as employee') ?></span>
                            </a>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= Yii::t('app', 'EMPLOYEE') ?></span>
                            <a href="<?= Url::to(['admin/promote', 'id' => $user->id]) ?>"
                               class="badge badge-success"
                               <?= Confirm::ask(Yii::t('app', 'Are you sure you want to promote this user for admin?')) ?>>
                                <?= FA::icon('hand-point-up') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'set as admin') ?></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user->status === User::STATUS_DELETED): ?>
                            <span class="badge badge-secondary"><?= Yii::t('app', 'DEACTIVATED') ?></span>
                            <a href="<?= Url::to(['admin/reactivate', 'id' => $user->id]) ?>"
                               class="badge badge-success"
                               <?= Confirm::ask(Yii::t('app', 'Are you sure you want to reactivate this user?')) ?>>
                                <?= FA::icon('toggle-on') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'reactivate') ?></span>
                            </a>
                        <?php else: ?>
                            <span class="badge badge-success"><?= Yii::t('app', 'ACTIVE') ?></span>
                            <a href="<?= Url::to(['admin/deactivate', 'id' => $user->id]) ?>"
                               class="badge badge-secondary"
                               <?= Confirm::ask(Yii::t('app', 'Are you sure you want to deactivate this user?')) ?>>
                                <?= FA::icon('toggle-off') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'deactivate') ?></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td class="text-right text-nowrap">
                        <?php if (Terminal::isActive()): ?>
                            <a href="<?= Url::to(['admin/terminal-edit', 'id' => $user->id]) ?>" class="btn btn-success btn-sm">
                                <?= FA::icon('desktop') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'add to terminal') ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($user->status !== User::STATUS_DELETED): ?>
                        <a href="<?= Url::to(['admin/reset', 'id' => $user->id]) ?>"
                           class="btn btn-warning btn-sm"
                           <?= Confirm::ask(Yii::t('app', 'Are you sure you want to send password reset link?')) ?>>
                            <?= FA::icon('key') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'reset password') ?></span>
                        </a>
                        <?php endif; ?>
                        <a href="<?= Url::to(['admin/delete', 'id' => $user->id]) ?>"
                           class="btn btn-danger btn-sm"
                           <?= Confirm::ask(Yii::t('app', 'Are you sure you want to delete this user?')) ?>>
                            <?= FA::icon('times') ?> <span class="d-none d-lg-inline"><?= Yii::t('app', 'delete') ?></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
