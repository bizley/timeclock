<?php

declare(strict_types=1);

namespace tests\api;

use app\models\User;
use tests\ApiTestCase;
use yii\web\ForbiddenHttpException;

/**
 * Class UserTest
 * @package tests\api
 */
class UserTest extends ApiTestCase
{
    /**
     * @var array
     */
    public $fixtures = [
        'user' => [
            [
                'id' => 1,
                'email' => 'employee@company.com',
                'name' => 'employee',
                'auth_key' => 'test',
                'password_hash' => 'test',
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_ACTIVE,
                'api_key' => 'apikey',
            ],
        ],
    ];

    public function testAuthentication(): void
    {
        $time = time();
        $token = '1:' . $time . ':' . sha1($time . 'apikey');

        $user = User::findIdentityByAccessToken($token);

        $this->assertSame(1, $user->id);
    }

    public function testWrongId(): void
    {
        $this->expectException(ForbiddenHttpException::class);

        $time = time();
        $token = '2:' . $time . ':' . sha1($time . 'apikey');

        User::findIdentityByAccessToken($token);
    }

    public function testWrongKey(): void
    {
        $this->expectException(ForbiddenHttpException::class);

        $time = time();
        $token = '1:' . $time . ':' . sha1($time . 'differentapikey');

        User::findIdentityByAccessToken($token);
    }

    public function testWrongTime(): void
    {
        $this->expectException(ForbiddenHttpException::class);

        $time = time() - 10000;
        $token = '1:' . $time . ':' . sha1($time . 'apikey');

        User::findIdentityByAccessToken($token);
    }
}
