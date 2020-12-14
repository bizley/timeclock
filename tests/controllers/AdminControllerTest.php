<?php

declare(strict_types=1);

namespace tests\controllers;

use app\controllers\AdminController;
use tests\AppTestCase;
use Yii;

use function date;
use function mktime;

/**
 * Class AdminControllerTest
 * @package tests\controllers
 */
class AdminControllerTest extends AppTestCase
{
    /**
     * @return array
     */
    public function monthsAndYearsProvider(): array
    {
        $thisMonth = mktime(6, 0, 0, (int)date('n'), 1, (int)date('Y'));
        $prevMonth = mktime(6, 0, 0, (int)date('n') - 1, 1, (int)date('Y'));
        $nextMonth = mktime(6, 0, 0, (int)date('n') + 1, 1, (int)date('Y'));

        return [
            [
                [
                    (int)date('n', $thisMonth),
                    (int)date('Y', $thisMonth),
                    (int)date('n', $prevMonth),
                    (int)date('Y', $prevMonth),
                    (int)date('n', $nextMonth),
                    (int)date('Y', $nextMonth),
                ],
                ['a', 'b'],
            ],
            [
                [
                    (int)date('n', $thisMonth),
                    (int)date('Y', $thisMonth),
                    (int)date('n', $prevMonth),
                    (int)date('Y', $prevMonth),
                    (int)date('n', $nextMonth),
                    (int)date('Y', $nextMonth),
                ],
                [0, 2017],
            ],
            [
                [
                    (int)date('n', $thisMonth),
                    (int)date('Y', $thisMonth),
                    (int)date('n', $prevMonth),
                    (int)date('Y', $prevMonth),
                    (int)date('n', $nextMonth),
                    (int)date('Y', $nextMonth),
                ],
                [13, 2017],
            ],
            [
                [1, 2018, 12, 2017, 2, 2018],
                [1, 2018],
            ],
            [
                [6, 2019, 5, 2019, 7, 2019],
                [6, 2019],
            ],
            [
                [12, 2020, 11, 2020, 1, 2021],
                [12, 2020],
            ],
        ];
    }

    /**
     * @dataProvider monthsAndYearsProvider
     * @param array $expected
     * @param array $provided
     */
    public function testMonthsAndYears(array $expected, array $provided): void
    {
        $data = new AdminController('admin', Yii::$app);

        self::assertSame($expected, $data->getMonthsAndYears($provided[0], $provided[1]));
    }

    /**
     * @return array
     */
    public function weekRangeProvider(): array
    {
        return [
            [
                [null, null, null, 5],
                [0, 3, 2019],
            ],
            [
                [null, null, null, 5],
                [null, 3, 2019],
            ],
            [
                [null, null, null, 5],
                [6, 3, 2019],
            ],
            [
                [1, 1, 3, 5],
                [1, 3, 2019],
            ],
            [
                [2, 4, 10, 5],
                [2, 3, 2019],
            ],
            [
                [3, 11, 17, 5],
                [3, 3, 2019],
            ],
            [
                [4, 18, 24, 5],
                [4, 3, 2019],
            ],
            [
                [5, 25, 31, 5],
                [5, 3, 2019],
            ],
            [
                [null, null, null, 6],
                [7, 12, 2018],
            ],
            [
                [1, 1, 2, 6],
                [1, 12, 2018],
            ],
            [
                [2, 3, 9, 6],
                [2, 12, 2018],
            ],
            [
                [5, 25, 31, 5],
                [5, 12, 2017],
            ],
            [
                [5, 26, 31, 5],
                [5, 12, 2016],
            ],
            [
                [1, 1, 6, 5],
                [1, 1, 2019],
            ],
            [
                [1, 1, 1, 6],
                [1, 1, 2017],
            ],
            [
                [1, 1, 3, 5],
                [1, 1, 2016],
            ],
        ];
    }

    /**
     * @dataProvider weekRangeProvider
     * @param array $expected
     * @param array $provided
     */
    public function testWeekRange(array $expected, array $provided): void
    {
        $data = new AdminController('admin', Yii::$app);

        self::assertSame($expected, $data->getWeekRange($provided[0], $provided[1], $provided[2]));
    }
}
