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
                'email' => 'employee@semfleet.tech',
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
            'emailDomain' => '@semfleet.tech'
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('Email "employee@semfleet.tech" has already been taken.', $registerForm->getFirstError('email'));
    }

    public function testPasswordSameAsAccount(): void
    {
        $registerForm = new RegisterForm([
            'password' => 'employee',
            'emailAccount' => 'employee',
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('Hasło must not be equal to "Email".', $registerForm->getFirstError('password'));
    }

    public function testPasswordTooSimple(): void
    {
        $registerForm = new RegisterForm([
            'password' => 'eeeeee',
        ]);

        $this->assertFalse($registerForm->validate());

        $this->assertSame('Musisz wybrać bardziej skomplikowane hasło.', $registerForm->getFirstError('password'));
    }
}
