<?php

declare(strict_types=1);

namespace tests\api;

use app\api\models\Off;
use app\models\User;
use tests\ApiTestCase;
use Yii;

/**
 * Class OffTest
 * @package tests\api
 */
class OffTest extends ApiTestCase
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
        'off' => [
            [
                'id' => 1,
                'user_id' => 1,
                'start_at' => '2018-12-05',
                'end_at' => '2018-12-06',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'start_at' => '2019-03-10',
                'end_at' => '2019-04-10',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Yii::$app->user->setIdentity(User::findOne(1));
    }

    public function testRequired(): void
    {
        $off = new Off();

        $this->assertFalse($off->validate());
        $this->assertSame('Start At cannot be blank.', $off->getFirstError('startAt'));
        $this->assertSame('End At cannot be blank.', $off->getFirstError('endAt'));
    }

    public function testEndAtInPast(): void
    {
        $off = new Off();

        $off->startAt = '2019-04-15';
        $off->endAt = '2019-03-01';

        $this->assertFalse($off->validate());
        $this->assertSame('Off-time ending day can not be earlier than starting day.', $off->getFirstError('endAt'));
    }

    public function testOverlappingWithAnotherUser(): void
    {
        $off = new Off();

        $off->startAt = '2019-03-13';
        $off->endAt = '2019-04-01';

        $this->assertTrue($off->validate());
    }

    public function testStartAtOverlapping(): void
    {
        $off = new Off();

        $off->startAt = '2018-12-06';
        $off->endAt = '2018-12-08';

        $this->assertFalse($off->validate());
        $this->assertSame('Can not start off-time because it overlaps with another off-time.', $off->getFirstError('startAt'));
    }

    public function testEndAtOverlapping(): void
    {
        $off = new Off();

        $off->startAt = '2018-12-01';
        $off->endAt = '2018-12-05';

        $this->assertFalse($off->validate());
        $this->assertSame('Can not end off-time because it overlaps with another off-time.', $off->getFirstError('endAt'));
    }

    public function testBetweenOverlapping(): void
    {
        $off = new Off();

        $off->startAt = '2018-12-01';
        $off->endAt = '2018-12-10';

        $this->assertFalse($off->validate());
        $this->assertSame('Can not modify off-time because it overlaps with another off-time.', $off->getFirstError('endAt'));
    }

    public function testUpdateOffTime(): void
    {
        $off = Off::findOne(1);

        $off->scenario = 'update';
        $off->startAt = '2018-12-10';
        $off->endAt = '2018-12-20';

        $this->assertTrue($off->save());

        $updatedOff = Off::findOne(1);

        $this->assertSame('2018-12-10', $updatedOff->startAt);
        $this->assertSame('2018-12-20', $updatedOff->endAt);
    }
}
