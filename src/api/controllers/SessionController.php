<?php

declare(strict_types=1);

namespace app\api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

/**
 * Class SessionController
 * @package app\api\controllers
 */
class SessionController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }
}
