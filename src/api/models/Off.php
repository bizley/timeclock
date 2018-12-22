<?php

declare(strict_types=1);

namespace app\api\models;

use app\models\User;
use Yii;

/**
 * Class Off
 * @package app\api\models
 */
class Off extends \app\models\Off
{
    /**
     * @var int
     */
    public $startAt;

    /**
     * @var int
     */
    public $endAt;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['startAt', 'endAt'], 'filter', 'filter' => 'intval'],
            [['startAt', 'endAt'], 'integer'],
            [['startAt', 'endAt'], 'required'],
            [['note'], 'string'],
            [['endAt'], 'compare', 'compareAttribute' => 'startAt', 'operator' => '>'],
            [['user_id'], 'required'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['startAt'], 'checkStartAt'],
            [['endAt'], 'checkEndAt'],
        ];
    }

    public function afterFind(): void
    {
        $this->startAt = $this->start_at;
        $this->endAt = $this->end_at;

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
            'startAt',
            'endAt',
            'note',
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

        return true;
    }

    public function afterValidate(): void
    {
        $this->start_at = $this->startAt;
        $this->end_at = $this->endAt;

        parent::afterValidate();
    }

    /**
     * @throws \Exception
     */
    public function checkStartAt(): void
    {
        $this->startAt = (new \DateTime(date('Y-m-d 00:00:00', $this->startAt), new \DateTimeZone(Yii::$app->timeZone)))->getTimestamp();

        $conditions = [
            'and',
            ['<=', 'start_at', $this->startAt],
            ['>=', 'end_at', $this->startAt],
        ];

        if ($this->scenario === 'update') {
            $conditions[] = ['<>', 'id', $this->id];
        }

        if (static::find()->where($conditions)->exists()) {
            $this->addError('startAt', Yii::t('app', 'Can not start off-time because it overlaps with another off-time.'));
        }
    }

    /**
     * @throws \Exception
     */
    public function checkEndAt(): void
    {
        $this->endAt = (new \DateTime(date('Y-m-d 23:59:59', $this->endAt), new \DateTimeZone(Yii::$app->timeZone)))->getTimestamp();

        $conditions = [
            'and',
            ['<=', 'start_at', $this->endAt],
            ['>=', 'end_at', $this->endAt],
        ];

        if ($this->scenario === 'update') {
            $conditions[] = ['<>', 'id', $this->id];
        }

        if (static::find()->where($conditions)->exists()) {
            $this->addError('endAt', Yii::t('app', 'Can not end off-time because it overlaps with another off-time.'));
        }
    }
}
