<?php

declare(strict_types=1);

namespace tests\models;

use app\models\Clock;
use app\models\User;
use tests\DbTestCase;
use Yii;

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
     * @throws \yii\db\Exception
     */
    protected function insert(array $row): void
    {
        static::$db->createCommand()->insert('clock', $row)->execute();
    }

    public function testIsAnotherSessionSaved(): void
    {
        $clock = new Clock([
            'user_id' => 1,
        ]);

        $clock->clock_out = 10;
        $this->assertTrue($clock->isAnotherSessionSaved());

        $clock->clock_out = 100;
        $this->assertTrue($clock->isAnotherSessionSaved());

        $clock->clock_out = 50;
        $this->assertTrue($clock->isAnotherSessionSaved());

        $clock->clock_out = 1;
        $this->assertFalse($clock->isAnotherSessionSaved());
    }

    public function testStart(): void
    {
        $this->assertTrue((new Clock())->start());

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        $this->assertEmpty($clock->clock_out);
    }

    /**
     * @runInSeparateProcess
     * @throws \yii\db\Exception
     */
    public function testStartWhenSessionAlreadyStarted(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $this->assertFalse((new Clock())->start());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testStartWhenSessionAlreadyStartedAndClosedToday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => (int) Yii::$app->formatter->asTimestamp('-3 minutes'),
        ]);

        $this->assertTrue((new Clock())->start());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testStartWhenSessionAlreadyStartedAndClosedYesterday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-24 hours'),
            'clock_out' => (int) Yii::$app->formatter->asTimestamp('-23 hours'),
        ]);

        $this->assertTrue((new Clock())->start());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testStartWhenSessionAlreadyStartedYesterday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-24 hours'),
            'clock_out' => null,
        ]);

        $this->assertTrue((new Clock())->start());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testStop(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        $this->assertTrue($clock->stop());

        $clock->refresh();

        $this->assertNotEmpty($clock->clock_out);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testStopBeforeStart(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('+5 minutes'),
            'clock_out' => null,
        ]);

        $clock = Clock::find()->orderBy(['id' => SORT_DESC])->one();

        $this->assertFalse($clock->stop());

        $clock->refresh();

        $this->assertEmpty($clock->clock_out);
    }

    /**
     * @runInSeparateProcess
     * @throws \yii\db\Exception
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

        $this->assertFalse($clock->stop());
    }

    public function testNoSession(): void
    {
        $this->assertEmpty(Clock::session());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testSessionActive(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $this->assertNotEmpty(Clock::session());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testSessionActiveYesterday(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-24 hours'),
            'clock_out' => null,
        ]);

        $this->assertEmpty(Clock::session());
    }

    public function testMonths(): void
    {
        $this->assertEquals([
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

        $this->assertFalse($mock->start());
    }
}
