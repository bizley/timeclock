<?php

declare(strict_types=1);

namespace tests;

use app\api\Api;
use app\base\Alert;
use app\models\User;

/**
 * Class ApiTestCase
 * @package tests
 */
abstract class ApiTestCase extends DbTestCase
{
    /**
     * @return array additional mocked app config
     * @throws \yii\db\Exception
     */
    public static function config(): array
    {
        return [
            'components' => [
                'db' => static::getConnection(),
                'alert' => Alert::class,
                'user' => [
                    'identityClass' => User::class,
                ],
            ],
            'modules' => [
                'api' => Api::class,
            ],
        ];
    }
}
