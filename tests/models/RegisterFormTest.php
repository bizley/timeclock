<?php

declare(strict_types=1);

namespace tests\models;

use app\models\RegisterForm;
use app\models\User;
use tests\DbTestCase;

/**
 * Class RegisterFormTest
 * @package tests\models
 */
class RegisterFormTest extends DbTestCase
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
            ],
        ],
    ];

    public function testWrongEmail(): void
    {
        $registerForm = new RegisterForm([
            'emailAccount' => 'test',
            'emailDomain' => '@wrong.com'
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('Email Domain is invalid.', $registerForm->getFirstError('emailDomain'));
    }

    public function testSameEmail(): void
    {
        $registerForm = new RegisterForm([
            'emailAccount' => 'employee',
            'emailDomain' => '@company.com'
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('Email "employee@company.com" has already been taken.', $registerForm->getFirstError('email'));
    }

    public function testPasswordSameAsAccount(): void
    {
        $registerForm = new RegisterForm([
            'password' => 'employee',
            'emailAccount' => 'employee',
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('Password must not be equal to "Email".', $registerForm->getFirstError('password'));
    }

    public function testPasswordTooSimple(): void
    {
        $registerForm = new RegisterForm([
            'password' => 'eeeeee',
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('You must provide more complex password.', $registerForm->getFirstError('password'));
    }
}
