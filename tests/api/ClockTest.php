<?php

declare(strict_types=1);

namespace tests\api;

use app\api\models\Clock;
use app\models\User;
use tests\ApiTestCase;
use Yii;

/**
 * Class ClockTest
 * @package tests\api
 */
class ClockTest extends ApiTestCase
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
            [
                'id' => 2,
                'email' => 'employee2@company.com',
                'name' => 'employee2',
                'auth_key' => 'test2',
                'password_hash' => 'test2',
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_ACTIVE,
            ],
        ],
        'clock' => [
            [
                'id' => 1,
                'user_id' => 1,
                'clock_in' => 10,
                'clock_out' => 100,
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'clock_in' => 1546434000, // 13:00
                'clock_out' => 1546440300, // 14:45
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'clock_in' => 1546441800, // 15:10
                'clock_out' => 1546447200, // 16:40
            ],
            [
                'id' => 4,
                'user_id' => 2,
                'clock_in' => 200,
                'clock_out' => 300,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Yii::$app->user->setIdentity(User::findOne(1));
    }

    public function testAutoClockIn(): void
    {
        $clock = new Clock();

        $clock->validate();

        self::assertNotEmpty($clock->clockIn);
    }

    public function testClockOutCanBeEmpty(): void
    {
        $clock = new Clock();

        self::assertTrue($clock->validate());
    }

    public function testClockOutInPast(): void
    {
        $clock = new Clock();

        $clock->clockOut = 1;

        self::assertFalse($clock->validate());
        self::assertSame('Clock Out must be greater than "Clock In".', $clock->getFirstError('clockOut'));
    }

    public function testClockOverlappingWithOtherUsers(): void
    {
        $clock = new Clock();

        $clock->clockIn = 210;
        $clock->clockOut = 290;

        self::assertTrue($clock->validate());
    }

    public function testClockInOverlapping(): void
    {
        $clock = new Clock();

        $clock->clockIn = 50;
        $clock->clockOut = 110;

        self::assertFalse($clock->validate());
        self::assertSame('Can not start session because it overlaps with another ended session.', $clock->getFirstError('clockIn'));
    }

    public function testClockInEdgeOverlapping(): void
    {
        $clock = new Clock();

        $clock->clockIn = 100;
        $clock->clockOut = 150;

        self::assertTrue($clock->validate());
    }

    public function testClockOutOverlapping(): void
    {
        $clock = new Clock();

        $clock->clockIn = 1;
        $clock->clockOut = 50;

        self::assertFalse($clock->validate());
        self::assertSame('Can not end session because it overlaps with another ended session.', $clock->getFirstError('clockOut'));
    }

    public function testClockOutEdgeOverlapping(): void
    {
        $clock = new Clock();

        $clock->clockIn = 1;
        $clock->clockOut = 10;

        self::assertTrue($clock->validate());
    }

    public function testClockBetweenOverlapping(): void
    {
        $clock = new Clock();

        $clock->clockIn = 5;
        $clock->clockOut = 150;

        self::assertFalse($clock->validate());
        self::assertSame('Can not modify session because it overlaps with another ended session.', $clock->getFirstError('clockOut'));
    }

    public function testOverMidnightSession(): void
    {
        $clock = new Clock();

        $clock->clockIn = 1545948000;
        $clock->clockOut = 1545987600;

        self::assertFalse($clock->validate());
        self::assertSame('Session can not last through midnight.', $clock->getFirstError('clockOut'));
    }

    public function testUpdateSession(): void
    {
        $clock = Clock::findOne(1);

        $clock->scenario = 'update';
        $clock->clockOut = 1000;

        self::assertTrue($clock->save());

        self::assertSame(1000, Clock::findOne(1)->clockOut);
    }

    public function testUpdateSessionOverlappingWithOtherUser(): void
    {
        $clock = Clock::findOne(1);

        $clock->scenario = 'update';
        $clock->clockOut = 250;

        self::assertTrue($clock->save());

        self::assertSame(250, Clock::findOne(1)->clockOut);
    }

    public function testUpdateSessionBefore(): void
    {
        $clock = Clock::findOne(2);

        $clock->scenario = 'update';
        $clock->clockOut = 1546440600; // 14:50

        self::assertTrue($clock->save());
    }
}
