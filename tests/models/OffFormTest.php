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
     */
    public function testPrepareDate(string $expected, int $year, int $month, int $day, int $hour, int $minute): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
        ]));

        $this->assertSame($expected, $offForm->prepareDate($year, $month, $day, $hour, $minute));
    }

    /**
     * @return array
     */
    public function maxDayProvider(): array
    {
        return [
            ['Wybrany miesiąc ma tylko 28 dni.', 2018, 2],
            ['Wybrany miesiąc ma tylko 29 dni.', 2016, 2],
            ['Wybrany miesiąc ma tylko 30 dni.', 2018, 4],
            ['Wybrany miesiąc ma tylko 31 dni.', 2018, 1],
        ];
    }

    /**
     * @dataProvider maxDayProvider
     * @param string $expected
     * @param int $year
     * @param int $month
     */
    public function testMaxDay(string $expected, int $year, int $month): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
        ]));

        $offForm->startDay = 32;
        $offForm->startYear = $year;
        $offForm->startMonth = $month;

        $offForm->maxDay('startDay');

        $this->assertSame($expected, $offForm->getFirstError('startDay'));
    }

    public function testVerifyStartOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1544054400,
        ]));

        $offForm->verifyStart();

        $this->assertSame('Wybrany dzień pokrywa się z innym okresem wolnym.', $offForm->getFirstError('startDay'));
    }

    public function testVerifyStartNoOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1240000500,
        ]));

        $offForm->verifyStart();

        $this->assertFalse($offForm->hasErrors());
    }

    public function testVerifyEnd(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1543622400,
        ]));

        $offForm->endYear = '2018';
        $offForm->endMonth = '12';
        $offForm->endDay = '3';

        $offForm->verifyEnd();

        $this->assertFalse($offForm->hasErrors());
    }

    public function testVerifyEndYearMissing(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
        ]));

        $offForm->endMonth = '12';
        $offForm->endDay = '3';

        $offForm->verifyEnd();

        $this->assertSame('Podaj rok zakończenia okresu wolnego.', $offForm->getFirstError('endYear'));
    }

    public function testVerifyEndMonthMissing(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
        ]));

        $offForm->endYear = '2018';
        $offForm->endDay = '3';

        $offForm->verifyEnd();

        $this->assertSame('Podaj miesiąc zakończenia okresu wolnego.', $offForm->getFirstError('endMonth'));
    }

    public function testVerifyEndDayMissing(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1,
        ]));

        $offForm->endYear = '2018';
        $offForm->endMonth = '12';

        $offForm->verifyEnd();

        $this->assertSame('Podaj dzień zakończenia okresu wolnego.', $offForm->getFirstError('endDay'));
    }

    public function testVerifyEndSwapped(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1543622400,
        ]));

        $offForm->endYear = '2018';
        $offForm->endMonth = '10';
        $offForm->endDay = '3';

        $offForm->verifyEnd();

        $this->assertSame('Dzień zakończenia wolnego nie może być wcześniejszy niż dzień rozpoczęcia.', $offForm->getFirstError('endDay'));
    }

    public function testVerifyEndOverlap(): void
    {
        $offForm = new OffForm(new Off([
            'user_id' => 1,
            'start_at' => 1543622400,
        ]));

        $offForm->endYear = '2018';
        $offForm->endMonth = '12';
        $offForm->endDay = '5';

        $offForm->verifyEnd();

        $this->assertSame('Wybrany dzień pokrywa się z innym okresem wolnym.', $offForm->getFirstError('endDay'));
    }
}
