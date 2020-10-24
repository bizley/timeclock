<?php

declare(strict_types=1);

namespace app\commands;

use app\models\Terminal;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Allows you to set any registered user as admin.
 * Class AdminController
 * @package app\commands
 */
class TerminalController extends Controller
{
    /**
     * Add terminal account
     * @return int
     * @throws Exception
     */
    public function actionAdd(): int
    {
        do {
            $apiKey = Yii::$app->security->generateRandomString(20);
        } while (Terminal::find()->where(['api_key' => $apiKey])->exists() ||
        User::find()->where(['api_key' => $apiKey])->exists());

        $terminal = new Terminal();
        $terminal->api_key = $apiKey;

        if (!$terminal->save()) {
            Yii::error($terminal->errors);
            $this->stdout("Error while saving new terminal account.\n");
            return ExitCode::SOFTWARE;
        }

        $this->stdout("A new terminal account was registered. Please note the API key: '{$terminal->api_key}'\n");
        return ExitCode::OK;
    }

    /**
     * Delete terminal account
     * @param $id
     * @return int
     */
    public function actionDelete($id): int
    {
        $account = Terminal::findOne(['id' => $id]);

        if (empty($account)) {
            $this->stdout("Can not find terminal account of given ID.\n");
            return ExitCode::SOFTWARE;
        } elseif ($account->delete() == false) {
            Yii::error($account->errors);
            $this->stdout("Error while deleting terminal account.\n");
            return ExitCode::SOFTWARE;
        }

        $this->stdout("Deleted terminal account of given ID successfully.\n");
        return ExitCode::OK;
    }

    /**
     * List all terminal accounts
     * @return int
     */
    public function actionList(): int
    {
        $accounts = Terminal::find()->orderBy(['id' => SORT_ASC])->all();

        if (empty($accounts)) {
            $this->stdout("No terminal accounts registered.\n");
        } else {
            $this->stdout("Following terminal accounts are currently registered:\n");
        }

        foreach ($accounts as $account) {
            $this->stdout("ID: '{$account->id}' \t API key: '{$account->api_key}'\n");
        }

        return ExitCode::OK;
    }
}
