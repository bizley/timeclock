<?php

declare(strict_types=1);

namespace app\base;

use Yii;

use function floor;

/**
 * Class ClockHelper
 * @package app\base
 */
class ClockHelper
{
    /**
     * Returns duration assuming day is 8 hours long.
     * @param int $value
     * @return string
     */
    public static function as8HrsDayDuration(int $value): string
    {
        $days = (int)floor($value / 8 / 60 / 60);

        return Yii::t('app', '{n,plural,one{# day} other{# days}}', ['n' => $days])
            . ', ' . Yii::$app->formatter->asDuration($value - $days * 8 * 60 * 60);
    }
}
