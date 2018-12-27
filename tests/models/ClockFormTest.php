<?php

declare(strict_types=1);

namespace tests\models;

use app\models\Clock;
use app\models\ClockForm;
use app\models\User;
use tests\DbTestCase;
use Yii;

/**
 * Class ClockFormTest
 * @package tests\models
 */
class ClockFormTest extends DbTestCase
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
        'clock' => [
            [
                'id' => 1,
                'user_id' => 1,
                'clock_in' => 1540000000,
                'clock_out' => 1540001000,
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
    public function roundToFiveProvider(): array
    {
        return [
            [0, 0],
            [0, 1],
            [0, 2],
            [5, 3],
            [5, 4],
            [5, 5],
            [5, 6],
            [5, 7],
            [10, 8],
        ];
    }

    /**
     * @dataProvider roundToFiveProvider
     * @param int $expected
     * @param int $provided
     * @throws \yii\base\InvalidConfigException
     */
    public function testRoundToFive(int $expected, int $provided): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1,
        ]));

        $this->assertSame($expected, $clockForm->roundToFive($provided));
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
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1,
        ]));

        $this->assertSame($expected, $clockForm->prepareDate($year, $month, $day, $hour, $minute));
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
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1,
        ]));

        $clockForm->day = 32;
        $clockForm->year = $year;
        $clockForm->month = $month;

        $clockForm->maxDay();

        $this->assertSame($expected, $clockForm->getFirstError('day'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyStartOverlap(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1540000500,
        ]));

        $clockForm->verifyStart();

        $this->assertSame('Selected hour overlaps another ended session.', $clockForm->getFirstError('startHour'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyStartNoOverlap(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1240000500,
        ]));

        $clockForm->verifyStart();

        $this->assertFalse($clockForm->hasErrors());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEnd(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1540000500,
        ]));

        $clockForm->endHour = '3';
        $clockForm->endMinute = '0';

        $clockForm->verifyEnd();

        $this->assertFalse($clockForm->hasErrors());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEndMinutesMissing(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1,
        ]));

        $clockForm->endHour = '15';
        $clockForm->endMinute = '';

        $clockForm->verifyEnd();

        $this->assertSame(0, $clockForm->endMinute);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEndHourMissing(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1,
        ]));

        $clockForm->endHour = '';
        $clockForm->endMinute = '15';

        $clockForm->verifyEnd();

        $this->assertSame('Provide session ending hour.', $clockForm->getFirstError('endHour'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEndSwapped(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1540000500,
        ]));

        $clockForm->endHour = '1';
        $clockForm->endMinute = '0';

        $clockForm->verifyEnd();

        $this->assertSame('Session ending hour must be later than starting hour.', $clockForm->getFirstError('endHour'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testVerifyEndOverlap(): void
    {
        $clockForm = new ClockForm(new Clock([
            'user_id' => 1,
            'clock_in' => 1540000500,
        ]));

        $clockForm->endHour = '1';
        $clockForm->endMinute = '57';

        $clockForm->verifyEnd();

        $this->assertSame('Selected hour overlaps another ended session.', $clockForm->getFirstError('endHour'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testRequired(): void
    {
        $clockForm = new ClockForm(new Clock());

        $clockForm->validate();

        $this->assertSame('Year must be a number.', $clockForm->getFirstError('year'));
        $this->assertSame('Month must be a number.', $clockForm->getFirstError('month'));
        $this->assertSame('Day must be a number.', $clockForm->getFirstError('day'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function testUpdateClock(): void
    {
        $clockForm = new ClockForm(Clock::findOne(1));

        $clockForm->endHour = 9;
        $clockForm->endMinute = 30;

        $this->assertTrue($clockForm->save());

        $saved = Clock::findOne(1);

        $this->assertSame(1540027800, $saved->clock_out);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testUpdateClockFail(): void
    {
        static::$db->createCommand()->insert('clock', [
            'user_id' => 1,
            'clock_in' => 1540002000,
            'clock_out' => 1540051200,
        ])->execute();

        $clockForm = new ClockForm(Clock::findOne(1));

        $clockForm->endHour = 9;

        $clockForm->verifyEnd();

        $this->assertSame('Selected hour overlaps another ended session.', $clockForm->getFirstError('endHour'));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function testSaveClockNoUser(): void
    {
        $clockForm = new ClockForm(new Clock([
            'clock_in' => 1540002000,
            'clock_out' => 1540051200,
        ]));

        $this->assertTrue($clockForm->save());

        $saved = Clock::findOne(['clock_in' => 1540002000]);

        $this->assertSame(1, $saved->user_id);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function testSaveValidationFail(): void
    {
        $clockForm = new ClockForm(new Clock());
        $this->assertFalse($clockForm->save());
    }
}
