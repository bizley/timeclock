<?php

declare(strict_types=1);

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Allows you to set any registered user as admin.
 * Class AdminController
 * @package app\commands
 */
class AdminController extends Controller
{
    /**
     * Sets user of given ID as admin.
     * @param int $id
     * @return int
     */
    public function actionSet(int $id): int
    {
        $user = User::findOne(['id' => $id]);

        if ($user === null) {
            $this->stdout("Can not find user of given ID.\n");

            return ExitCode::DATAERR;
        }

        $user->role = User::ROLE_ADMIN;

        if (!$user->save()) {
            Yii::error($user->errors);
            $this->stdout("Error while saving user.\n");

            return ExitCode::SOFTWARE;
        }

        $this->stdout("User with email '{$user->email}' has been set as admin.\n");

        return ExitCode::OK;
    }
}
