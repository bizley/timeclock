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
 * @property string $note
 * @property int $created_at
 * @property int $updated_at
 */
class Off extends ActiveRecord
{
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
            [['note'], 'string'],
        ];
    }
}
