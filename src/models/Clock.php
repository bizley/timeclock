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
     * @return array
     */
    public static function months(): array
    {
        return [
            1 => Yii::t('app', 'January'),
            2 => Yii::t('app', 'February'),
            3 => Yii::t('app', 'March'),
            4 => Yii::t('app', 'April'),
            5 => Yii::t('app', 'May'),
            6 => Yii::t('app', 'June'),
            7 => Yii::t('app', 'July'),
            8 => Yii::t('app', 'August'),
            9 => Yii::t('app', 'September'),
            10 => Yii::t('app', 'October'),
            11 => Yii::t('app', 'November'),
            12 => Yii::t('app', 'December'),
        ];
    }

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

        return static::find()->where($conditions)->exists();
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
            Yii::$app->alert->danger(Yii::t('app', 'Session has already been started.'));
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
            Yii::$app->alert->danger(Yii::t('app', 'Can not end current session because it overlaps with another ended session.'));
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
