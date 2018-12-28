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
                'email' => 'employee@semfleet.tech',
                'name' => 'employee',
                'auth_key' => 'test',
                'password_hash' => 'test',
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_ACTIVE,
            ],
        ],
        'off' => [
            [
                'id' => 1,
                'user_id' => 1,
                'start_at' => 1543968000, // 2018-12-05 00:00:00
                'end_at' => 1544140740, // 2018-12-06 23:59:00
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

        $off->startAt = 10;
        $off->endAt = 1;

        $this->assertFalse($off->validate());
        $this->assertSame('End At must be greater than "Start At".', $off->getFirstError('endAt'));
    }

    public function testStartAtOverlapping(): void
    {
        $off = new Off();

        $off->startAt = 1543968010;
        $off->endAt = 1544140840;

        $this->assertFalse($off->validate());
        $this->assertSame('Can not start off-time because it overlaps with another off-time.', $off->getFirstError('startAt'));
    }

    public function testEndAtOverlapping(): void
    {
        $off = new Off();

        $off->startAt = 1543967000;
        $off->endAt = 1543968010;

        $this->assertFalse($off->validate());
        $this->assertSame('Can not end off-time because it overlaps with another off-time.', $off->getFirstError('endAt'));
    }

    public function testUpdateOffTime(): void
    {
        $off = Off::findOne(1);

        $off->scenario = 'update';
        $off->startAt = 1543967000;
        $off->endAt = 1544141740;

        $this->assertTrue($off->save());

        $updatedOff = Off::findOne(1);

        $this->assertSame(1543881600, $updatedOff->startAt);
        $this->assertSame(1544227199, $updatedOff->endAt);
    }
}
