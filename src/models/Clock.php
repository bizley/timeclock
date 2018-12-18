<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Clock model
 *
 * @property int $id
 * @property int $user_id
 * @property int $clock_in
 * @property int $clock_out
 * @property int $created_at
 * @property int $updated_at
 */
class Clock extends ActiveRecord
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
        return '{{%clock}}';
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
            [['user_id', 'clock_in'], 'required'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['clock_out'], 'compare', 'compareAttribute' => 'clock_in', 'operator' => '>'],
        ];
    }

    /**
     * @return bool
     */
    public function isAnotherSessionSaved(): bool
    {
        $conditions = [
            'and',
            ['user_id' => Yii::$app->user->id],
            ['<=', 'clock_in', $this->clock_out],
            ['>=', 'clock_out', $this->clock_out],
        ];

        return Clock::find()->where($conditions)->exists();
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        if (static::find()->where([
            'and',
            ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp('now')],
            ['>', 'clock_in', (int) Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
            [
                'clock_out' => null,
                'user_id' => Yii::$app->user->id,
            ],
        ])->exists()) {
            Yii::$app->alert->danger('Sesja została już rozpoczęta.');
            return false;
        }

        $this->clock_in = Yii::$app->formatter->asTimestamp('now');
        $this->clock_out = null;
        $this->user_id = Yii::$app->user->id;

        if (!$this->validate()) {
            Yii::error($this->errors);
            return false;
        }

        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        $this->clock_out = Yii::$app->formatter->asTimestamp('now');

        if (!$this->validate()) {
            Yii::error($this->errors);
            return false;
        }

        if ($this->isAnotherSessionSaved()) {
            Yii::$app->alert->danger('Nie można zakończyć aktualnej sesji, ponieważ pokrywa się one z inną już zamkniętą.');
            return false;
        }

        return $this->save(false);
    }

    /**
     * @return Clock|null
     */
    public static function session(): ?self
    {
        return static::find()->where([
            'and',
            ['<', 'clock_in', (int) Yii::$app->formatter->asTimestamp('now')],
            ['>', 'clock_in', (int) Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
            [
                'clock_out' => null,
                'user_id' => Yii::$app->user->id,
            ],
        ])->orderBy(['clock_in' => SORT_DESC])->one();
    }
}
