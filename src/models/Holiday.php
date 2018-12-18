<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Holiday model
 *
 * @property int $year
 * @property int $month
 * @property int $day
 */
class Holiday extends ActiveRecord
{
    /**
     * @var string
     */
    public static $calendarUrl = 'https://www.kalendarzswiat.pl/swieta/wolne_od_pracy/';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%holiday}}';
    }

    /**
     * @param int $year
     * @return array
     */
    public static function readHolidays(int $year): array
    {
        $holidays = [];

        $calendar = file_get_contents(static::$calendarUrl . $year);
        if ($calendar !== false) {
            preg_match_all('/data\-date\="([\d\-]+)"/', $calendar, $matches);

            if ($matches) {
                $holidays = $matches[1];
            }
        }

        return $holidays;
    }

    /**
     * @param int $year
     * @return bool
     */
    public static function isYearPopulated(int $year): bool
    {
        return static::find()->where(['year' => $year])->exists();
    }

    /**
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function getMonthHolidays(int $month, int $year): array
    {
        if (!static::isYearPopulated($year)) {
            static::populateYear($year);
        }

        return static::getHolidaysInMonth($month, $year);
    }

    /**
     * @param int $year
     */
    public static function populateYear(int $year): void
    {
        try {
            $holidays = static::readHolidays($year);

            foreach ($holidays as $holiday) {
                $date = explode('-', $holiday);
                if (count($date) === 3 && !static::find()->where([
                        'year' => (int) $date[0],
                        'month' => (int) $date[1],
                        'day' => (int) $date[2],
                    ])->exists()) {
                    $day = new static();

                    $day->year = (int) $date[0];
                    $day->month = (int) $date[1];
                    $day->day = (int) $date[2];

                    $day->save();
                }
            }
        } catch (\Throwable $exception) {
            \Yii::error($exception);
        }
    }

    /**
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function getHolidaysInMonth(int $month, int $year): array
    {
        $days = [];

        $holidays = static::find()->where([
            'month' => $month,
            'year' => $year,
        ])->all();

        foreach ($holidays as $holiday) {
            $days[] = $holiday->day;
        }

        return $days;
    }
}
