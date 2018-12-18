<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Off model
 *
 * @property int $id
 * @property int $user_id
 * @property int $start_at
 * @property int $end_at
 * @property int $created_at
 * @property int $updated_at
 */
class Off extends ActiveRecord
{
    /**
     * @var array
     */
    public static $months = [
        1 => 'styczeń',
        2 => 'luty',
        3 => 'marzec',
        4 => 'kwiecień',
        5 => 'maj',
        6 => 'czerwiec',
        7 => 'lipiec',
        8 => 'sierpień',
        9 => 'wrzesień',
        10 => 'październik',
        11 => 'listopad',
        12 => 'grudzień',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%off}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'start_at'], 'required'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['end_at'], 'compare', 'compareAttribute' => 'start_at', 'operator' => '>'],
        ];
    }
}
