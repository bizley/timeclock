<?php

declare(strict_types=1);

namespace tests\models;

use app\models\User;
use tests\DbTestCase;
use Yii;

/**
 * Class UserTest
 * @package tests\models
 */
class UserTest extends DbTestCase
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
        'clock' => [],
    ];

    /**
     * @param array $row
     * @throws \yii\db\Exception
     */
    protected function insert(array $row): void
    {
        static::$db->createCommand()->insert('clock', $row)->execute();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return User::findOne(1);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testIsClockActivePositive(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $this->assertTrue($this->getUser()->isClockActive());
    }

    public function testIsClockActiveNegative(): void
    {
        $this->assertFalse($this->getUser()->isClockActive());
    }

    public function testSessionStartedAtNone(): void
    {
        $this->assertEmpty($this->getUser()->sessionStartedAt());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testSessionStartedAt(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $this->assertNotEmpty($this->getUser()->sessionStartedAt());
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testTodaysSessions(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-25 minutes'),
            'clock_out' => (int) Yii::$app->formatter->asTimestamp('-15 minutes'),
        ]);
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp('-5 minutes'),
            'clock_out' => null,
        ]);

        $sessions = $this->getUser()->todaysSessions();

        $this->assertCount(2, $sessions);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testGetOldestOpenedSession(): void
    {
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp(date('2017-03-01 15:40:00')),
            'clock_out' => null,
        ]);
        $this->insert([
            'user_id' => 1,
            'clock_in' => (int) Yii::$app->formatter->asTimestamp(date('2017-01-01 10:00:00')),
            'clock_out' => null,
        ]);

        $this->assertSame((int) Yii::$app->formatter->asTimestamp(date('2017-01-01 10:00:00')), $this->getUser()->getOldOpenedSession()->clock_in);
    }
}
