<?php

declare(strict_types=1);

namespace tests\models;

use app\models\Clock;
use app\models\User;
use tests\DbTestCase;
use Yii;
use yii\db\Exception;

/**
 * Class ClockTest
 * @package tests\models
 */
class ClockTest extends DbTestCase
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
                'user_id' => 1,
                'clock_in' => 10,
                'clock_out' => 100,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Yii::$app->user->setIdentity(User::findOne(1));
    }

    /**
     * @param array $row
     * @throws Exception
     */
    protected function insert(array $row): void
    {
        static::$db->createCommand()->insert('clock', $row)->execute();
    }

    public function testIsAnotherSessionSaved(): void
    {
        $clock = new Clock([
            'user_id' => 1,
            'clock_in' => 5,
        ]);

        $clock->clock_out = 10;
        self::assertFalse($clock->isAnotherSessionSaved());

        $clock->clock_out = 100;
        self::assertTrue($clock->isAnotherSessionSaved());

        $clock->clock_out = 50;
        self::assertTrue($clock->isAnotherSessionSaved());

        $clock->clock_out = 1;
        self::assertFalse($clock->isAnotherSessionSaved());

        $clock->clock_out = 150;
        self::assertTrue($clock->isAnotherSessionSaved());
    }

    public function testStart(): void
    {
        self::assertTrue((new Clock())->start());

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        self::assertEmpty($clock->clock_out);
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testStartWhenSessionAlreadyStarted(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        self::assertFalse((new Clock())->start());
    }

    /**
     * @throws Exception
     */
    public function testStartWhenSessionAlreadyStartedAndClosedToday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => (int) Yii::$app->formatter->asTimestamp('-3 minutes'),
        ]);

        self::assertTrue((new Clock())->start());
    }

    /**
     * @throws Exception
     */
    public function testStartWhenSessionAlreadyStartedAndClosedYesterday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-24 hours'),
            'clock_out' => (int) Yii::$app->formatter->asTimestamp('-23 hours'),
        ]);

        self::assertTrue((new Clock())->start());
    }

    /**
     * @throws Exception
     */
    public function testStartWhenSessionAlreadyStartedYesterday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-24 hours'),
            'clock_out' => null,
        ]);

        self::assertTrue((new Clock())->start());
    }

    /**
     * @throws Exception
     */
    public function testStop(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        self::assertTrue($clock->stop());

        $clock->refresh();

        self::assertNotEmpty($clock->clock_out);
    }

    /**
     * @throws Exception
     */
    public function testStopBeforeStart(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('+5 minutes'),
            'clock_out' => null,
        ]);

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        self::assertFalse($clock->stop());

        $clock->refresh();

        self::assertEmpty($clock->clock_out);
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testStopWhenAnotherSession(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => (int) Yii::$app->formatter->asTimestamp('+5 minutes'),
        ]);
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-15 minutes'),
            'clock_out' => null,
        ]);

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        self::assertFalse($clock->stop());
    }

    public function testNoSession(): void
    {
        self::assertEmpty(Clock::session());
    }

    /**
     * @throws Exception
     */
    public function testSessionActive(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        self::assertNotEmpty(Clock::session());
    }

    /**
     * @throws Exception
     */
    public function testSessionActiveYesterday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-24 hours'),
            'clock_out' => null,
        ]);

        self::assertEmpty(Clock::session());
    }

    public function testMonths(): void
    {
        self::assertEquals([
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ], Clock::months());
    }

    public function testStartValidation(): void
    {
        $mock = $this->getMockBuilder(Clock::class)->setMethods(['validate'])->getMock();
        $mock->method('validate')->willReturn(false);

        self::assertFalse($mock->start());
    }
}
