<?php

declare(strict_types=1);

namespace app\api\models;

use app\models\User;
use Exception;
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
            [['type'], 'default', 'value' => self::TYPE_SHORT],
            [['startAt', 'endAt', 'user_id', 'type'], 'required'],
            [['note'], 'string'],
            [['type'], 'in', 'range' => [self::TYPE_SHORT, self::TYPE_VACATION]],
            [['endAt', 'startAt'], 'date', 'format' => 'yyyy-MM-dd'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['startAt'], 'checkStartAt'],
            [['endAt'], 'checkEndAt'],
            [['endAt'], 'checkBetween'],
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
            'type',
            'approved',
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

        if (empty($this->note)) {
            $this->note = null;
        }

        parent::afterValidate();
    }

    /**
     * @throws Exception
     */
    public function checkStartAt(): void
    {
        if (!$this->hasErrors()) {
            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
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
    }

    /**
     * @throws Exception
     */
    public function checkEndAt(): void
    {
        if (!$this->hasErrors()) {
            if (Yii::$app->formatter->asTimestamp($this->startAt) > Yii::$app->formatter->asTimestamp($this->endAt)) {
                $this->addError('endAt', Yii::t('app', 'Off-time ending day can not be earlier than starting day.'));
            } else {
                $conditions = [
                    'and',
                    ['user_id' => Yii::$app->user->id],
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
    }

    /**
     * @throws Exception
     */
    public function checkBetween(): void
    {
        if (!$this->hasErrors()) {
            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
                ['>=', 'start_at', $this->startAt],
                ['<=', 'end_at', $this->endAt],
            ];

            if ($this->scenario === 'update') {
                $conditions[] = ['<>', 'id', $this->id];
            }

            if (static::find()->where($conditions)->exists()) {
                $this->addError('endAt', Yii::t('app', 'Can not modify off-time because it overlaps with another off-time.'));
            }
        }
    }
}
