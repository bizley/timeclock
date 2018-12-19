<?php

declare(strict_types=1);

namespace tests\models;

use app\models\Off;
use app\models\OffForm;
use app\models\User;
use tests\DbTestCase;
use Yii;

/**
 * Class OffFormTest
 * @package tests\models
 */
class OffFormTest extends DbTestCase
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

    /**
     * @return array
     */
    public function prepareDateProvider(): array
    {
        return [
            ['2018-10-10 10:10:00', 2018, 10, 10, 10, 10],
            ['2018-01-01 01:01:00', 2018, 1, 1, 1, 1],
        ];
    }

    /**
     * @dataProvider prepareDateProvider
     * @param string $expected
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @throws \yii\base\InvalidConfigException
     */
    public function testPrepareDate(string $expected, int $year, int $month, int $day, int $hour, int $minute): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
            'end_at' => 10,
        ]));

        $this->assertSame($expected, $offForm->prepareDate($year, $month, $day, $hour, $minute));
    }

    /**
     * @return array
     */
    public function maxDayProvider(): array
    {
        return [
            ['Selected month has got only 28 days.', 2018, 2],
            ['Selected month has got only 29 days.', 2016, 2],
            ['Selected month has got only 30 days.', 2018, 4],
            ['Selected month has got only 31 days.', 2018, 1],
        ];
    }

    /**
     * @dataProvider maxDayProvider
     * @param string $expected
     * @param int $year
     * @param int $month
     * @throws \yii\base\InvalidConfigException
     */
    public function testMaxDay(string $expected, int $year, int $month): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
            'end_at' => 10,
        ]));

        $offForm->startDay = 32;
        $offForm->startYear = $year;
        $offForm->startMonth = $month;

        $offForm->maxDay('startDay');

        $this->assertSame($expected, $offForm->getFirstError('startDay'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyStartOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1544054400,
            'end_at' => 1544054401,
        ]));

        $offForm->verifyStart();

        $this->assertSame('Selected day overlaps another off-time.', $offForm->getFirstError('startDay'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyStartNoOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1240000500,
            'end_at' => 1240000501,
        ]));

        $offForm->verifyStart();

        $this->assertFalse($offForm->hasErrors());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEnd(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1543622400,
            'end_at' => 1543622401,
        ]));

        $offForm->endYear = '2018';
        $offForm->endMonth = '12';
        $offForm->endDay = '3';

        $offForm->verifyEnd();

        $this->assertFalse($offForm->hasErrors());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEndSwapped(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1543622400,
            'end_at' => 1543621000,
        ]));

        $offForm->verifyEnd();

        $this->assertSame('Off-time ending day can not be earlier than starting day.', $offForm->getFirstError('endDay'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEndOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1543622400,
            'end_at' => 1544140700,
        ]));

        $offForm->verifyEnd();

        $this->assertSame('Selected day overlaps another off-time.', $offForm->getFirstError('endDay'));
    }
}
