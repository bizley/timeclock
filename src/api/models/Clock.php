<?php

declare(strict_types=1);

namespace app\api\models;

use app\models\User;
use Yii;

/**
 * Class Clock
 * @package app\api\models
 */
class Clock extends \app\models\Clock
{
    /**
     * @var int
     */
    public $clockIn;

    /**
     * @var int
     */
    public $clockOut;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['clockIn'], 'required'], // can be null before validate
            [['clockIn', 'clockOut'], 'integer'],
            [['clockOut'], 'compare', 'compareAttribute' => 'clockIn', 'operator' => '>'],
            [['user_id'], 'required'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['clockIn'], 'checkClockIn'],
            [['clockOut'], 'checkClockOut'],
            [['clockOut'], 'checkOverMidnight'],
        ];
    }

    public function afterFind(): void
    {
        $this->clockIn = $this->clock_in;
        $this->clockOut = $this->clock_out;

        parent::afterFind();
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id',
            'userId' => 'user_id',
            'clockIn',
            'clockOut',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();

        $scenarios['update'] = $scenarios[self::SCENARIO_DEFAULT];

        return $scenarios;
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->user_id = Yii::$app->user->id;

        if ($this->clockIn === null) {
            $this->clockIn = (int) Yii::$app->formatter->asTimestamp('now');
        }

        $this->clockIn = (int) $this->clockIn;
        if ($this->clockOut !== null) {
            $this->clockOut = (int) $this->clockOut;
        }

        return true;
    }

    public function afterValidate(): void
    {
        $this->clock_in = $this->clockIn;
        $this->clock_out = $this->clockOut;

        parent::afterValidate();
    }

    public function checkClockIn(): void
    {
        if (!$this->hasErrors()) {
            $conditions = [
                'and',
                ['<=', 'clock_in', $this->clockIn],
                ['>=', 'clock_out', $this->clockIn],
            ];

            if ($this->scenario === 'update') {
                $conditions[] = ['<>', 'id', $this->id];
            }

            if (static::find()->where($conditions)->exists()) {
                $this->addError('clockIn', Yii::t('app', 'Can not start session because it overlaps with another ended session.'));
            }
        }
    }

    public function checkClockOut(): void
    {
        if ($this->clockOut !== null && !$this->hasErrors()) {
            $conditions = [
                'and',
                ['<=', 'clock_in', $this->clockOut],
                ['>=', 'clock_out', $this->clockOut],
            ];

            if ($this->scenario === 'update') {
                $conditions[] = ['<>', 'id', $this->id];
            }

            if (static::find()->where($conditions)->exists()) {
                $this->addError('clockIn', Yii::t('app', 'Can not end session because it overlaps with another ended session.'));
            }
        }
    }

    public function checkOverMidnight(): void
    {
        if ($this->clockOut !== null && !$this->hasErrors()) {
            $clockInDay = \DateTime::createFromFormat('Ymd', (string)$this->clockIn, new \DateTimeZone(Yii::$app->timeZone));
            $clockOutDay = \DateTime::createFromFormat('Ymd', (string)$this->clockOut, new \DateTimeZone(Yii::$app->timeZone));

            if ($clockInDay !== $clockOutDay) {
                $this->addError('clockOut', Yii::t('app', 'Session can not last through midnight.'));
            }
        }
    }
}
