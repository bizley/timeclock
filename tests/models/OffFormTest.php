<?php

declare(strict_types=1);

namespace tests\models;

use app\models\Off;
use app\models\OffForm;
use app\models\User;
use Exception;
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
                'email' => 'employee@company.com',
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
                'start_at' => '2018-12-05',
                'end_at' => '2018-12-06',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Yii::$app->user->setIdentity(User::findOne(1));
    }

    /**
     * @throws Exception
     */
    public function testVerifyStartOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => '2018-12-06',
            'end_at' => '2018-12-10',
        ]));

        $offForm->verifyStart();

        $this->assertSame('Selected day overlaps another off-time.', $offForm->getFirstError('startDate'));
    }

    /**
     * @throws Exception
     */
    public function testVerifyStartNoOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => '2018-12-01',
            'end_at' => '2018-12-03',
        ]));

        $offForm->verifyStart();

        $this->assertFalse($offForm->hasErrors());
    }

    /**
     * @throws Exception
     */
    public function testVerifyEndOk(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => '2018-12-20',
            'end_at' => '2018-12-21',
        ]));

        $offForm->endDate = '2018-12-23';

        $offForm->verifyEnd();

        $this->assertFalse($offForm->hasErrors());
    }

    /**
     * @throws Exception
     */
    public function testVerifyEndSwapped(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => '2018-12-20',
            'end_at' => '2018-12-18',
        ]));

        $offForm->verifyEnd();

        $this->assertSame('Off-time ending day can not be earlier than starting day.', $offForm->getFirstError('endDate'));
    }

    /**
     * @throws Exception
     */
    public function testVerifyEndOverlap(): void
    {
        $offForm = new OffForm(new Off());

        $offForm->startDate = '2018-12-04';
        $offForm->endDate = '2018-12-05';

        $offForm->verifyEnd();

        $this->assertSame('Selected day overlaps another off-time.', $offForm->getFirstError('endDate'));
    }
}
