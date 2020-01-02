<?php

declare(strict_types=1);

namespace tests\models;

use app\models\Clock;
use app\models\ClockForm;
use app\models\User;
use Exception;
use tests\DbTestCase;
use Yii;
use yii\base\InvalidConfigException;

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
                'email' => 'employee@company.com',
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
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Yii::$app->user->setIdentity(User::findOne(1));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testPrepareDateTime(): void
    {
        $clockForm = new ClockForm(new Clock());

        $date = $clockForm->prepareDateTime('2019-04-11 09:35');

        $this->assertSame(Yii::$app->timeZone, $date->getTimezone()->getName());
    }

    /**
     * @return array
     */
    public function prepareDateTimesProvider(): array
    {
        return [
            ['UTC', '2019-07-15 15:00', 1563202800, 1563213600],
            ['UTC', '2019-01-15 15:00', 1547564400, 1547575200],
            ['Europe/Warsaw', '2019-07-15 15:00', 1563195600, 1563206400],
            ['Europe/Warsaw', '2019-01-15 15:00', 1547560800, 1547571600],
            ['America/Chicago', '2019-07-15 15:00', 1563220800, 1563231600],
            ['America/Chicago', '2019-01-15 15:00', 1547586000, 1547596800],
        ];
    }

    /**
     * @dataProvider prepareDateTimesProvider
     * @param string $timezone
     * @param string $start
     * @param int $expected
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testPrepareStartTimestamp(string $timezone, string $start, int $expected): void
    {
        Yii::$app->timeZone = $timezone;

        $clockForm = new ClockForm(new Clock());
        $clockForm->startDate = $start;

        $this->assertSame($expected, $clockForm->prepareStart());

        Yii::$app->timeZone = 'UTC';
    }

    /**
     * @dataProvider prepareDateTimesProvider
     * @param string $timezone
     * @param string $start
     * @param int $expectedStart
     * @param int $expectedEnd
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testPrepareEndTimestamp(string $timezone, string $start, int $expectedStart, int $expectedEnd): void
    {
        Yii::$app->timeZone = $timezone;

        $clockForm = new ClockForm(new Clock());
        $clockForm->startDate = $start;
        $clockForm->endTime = '18:00';

        $this->assertSame($expectedEnd, $clockForm->prepareEnd());

        Yii::$app->timeZone = 'UTC';
    }

    /**
     * @return array
     */
    public function prepareBadTimesProvider(): array
    {
        return [
            [''],
            ['aaa'],
            ['11:aa'],
            ['30:00'],
            ['11:60'],
            ['24:00'],
        ];
    }

    /**
     * @dataProvider prepareBadTimesProvider
     * @param string $time
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testVerifyTimeBad(string $time): void
    {
        $clockForm = new ClockForm(new Clock());
        $clockForm->endTime = $time;

        $clockForm->verifyTime();

        $this->assertSame('Please provide proper time in HH:MM format.', $clockForm->getFirstError('endTime'));
    }

    /**
     * @return array
     */
    public function prepareGoodTimesProvider(): array
    {
        return [
            ['00:00'],
            ['20:10'],
            ['23:59'],
        ];
    }

    /**
     * @dataProvider prepareGoodTimesProvider
     * @param string $time
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testVerifyTimeOk(string $time): void
    {
        $clockForm = new ClockForm(new Clock());
        $clockForm->endTime = $time;

        $clockForm->verifyTime();
        $this->assertEmpty($clockForm->getFirstError('endTime'));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testVerifyStartOverlap(): void
    {
        $clockForm = new ClockForm(new Clock());

        $clockForm->startDate = '2019-01-02 13:10';
        $clockForm->verifyStart();
        $this->assertSame('Selected hour overlaps another ended session.', $clockForm->getFirstError('startDate'));
        $clockForm->clearErrors('startDate');

        $clockForm->startDate = '2019-01-02 13:00';
        $clockForm->verifyStart();
        $this->assertSame('Selected hour overlaps another ended session.', $clockForm->getFirstError('startDate'));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testVerifyStartNoOverlap(): void
    {
        $clockForm = new ClockForm(new Clock());

        $clockForm->startDate = '2019-01-02 12:00';
        $clockForm->verifyStart();
        $this->assertEmpty($clockForm->getFirstError('startDate'));

        $clockForm->startDate = '2019-01-02 14:50';
        $clockForm->verifyStart();
        $this->assertEmpty($clockForm->getFirstError('startDate'));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testVerifyEndNoOverlap(): void
    {
        $clockForm = new ClockForm(new Clock());
        $clockForm->startDate = '2019-01-02 14:50';

        $clockForm->endTime = '15:00';
        $clockForm->verifyEnd();
        $this->assertEmpty($clockForm->getFirstError('endTime'));

        $clockForm->endTime = '15:10';
        $clockForm->verifyEnd();
        $this->assertEmpty($clockForm->getFirstError('endTime'));

        $clockForm->startDate = '2019-01-02 14:45';
        $clockForm->endTime = '15:00';
        $clockForm->verifyEnd();
        $this->assertEmpty($clockForm->getFirstError('endTime'));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testVerifyEndOverlap(): void
    {
        $clockForm = new ClockForm(new Clock());
        $clockForm->startDate = '2019-01-02 14:50';

        $clockForm->endTime = '14:49';
        $clockForm->verifyEnd();
        $this->assertSame('Session ending hour must be later than starting hour.', $clockForm->getFirstError('endTime'));
        $clockForm->clearErrors('endTime');

        $clockForm->endTime = '15:20';
        $clockForm->verifyEnd();
        $this->assertSame('Selected session time overlaps another ended session.', $clockForm->getFirstError('endTime'));
        $clockForm->clearErrors('endTime');

        $clockForm->endTime = '16:40';
        $clockForm->verifyEnd();
        $this->assertSame('Selected session time overlaps another ended session.', $clockForm->getFirstError('endTime'));
        $clockForm->clearErrors('endTime');

        $clockForm->endTime = '16:50';
        $clockForm->verifyEnd();
        $this->assertSame('Selected session time overlaps another ended session.', $clockForm->getFirstError('endTime'));
    }

    /**
     * @throws InvalidConfigException
     */
    public function testRequired(): void
    {
        $clockForm = new ClockForm(new Clock());

        $clockForm->validate();

        $this->assertSame('Start cannot be blank.', $clockForm->getFirstError('startDate'));
        $this->assertEmpty($clockForm->getFirstError('endTime'));
        $this->assertEmpty($clockForm->getFirstError('note'));
        $this->assertEmpty($clockForm->getFirstError('projectId'));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testUpdateClock(): void
    {
        $clockForm = new ClockForm(Clock::findOne(1));
        $clockForm->endTime = '09:30';

        $this->assertTrue($clockForm->save());

        $saved = Clock::findOne(1);

        $this->assertSame(1540027800, $saved->clock_out);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function testUpdateClockFail(): void
    {
        static::$db->createCommand()->insert('clock', [
            'user_id' => 1,
            'clock_in' => 1540002000,
            'clock_out' => 1540051200,
        ])->execute();

        $clockForm = new ClockForm(Clock::findOne(1));
        $clockForm->endTime = '09:00';

        $clockForm->verifyEnd();

        $this->assertSame('Selected session time overlaps another ended session.', $clockForm->getFirstError('endTime'));
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSaveValidationFail(): void
    {
        $clockForm = new ClockForm(new Clock());
        $this->assertFalse($clockForm->save());
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testUpdateWithTimezoneClock(): void
    {
        Yii::$app->timeZone = 'Europe/Warsaw';

        $clockForm = new ClockForm(Clock::findOne(2));

        $clockForm->endTime = '15:50';

        $this->assertTrue($clockForm->save());

        $saved = Clock::findOne(2);

        $this->assertSame(1546440600, $saved->clock_out);
    }
}
