<?php

declare(strict_types=1);

namespace app\api;

use Yii;
use yii\base\Module;

/**
 * Class Api
 * @package app\api
 */
class Api extends Module
{
    public function init(): void
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        Yii::$app->request->enableCsrfCookie = false;
    }
}
