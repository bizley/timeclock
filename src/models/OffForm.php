<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;

/**
 * Class OffForm
 * @package app\models
 */
class OffForm extends Model
{
    /**
     * @var string
     */
    public $startDate;

    /**
     * @var string
     */
    public $endDate;

    /**
     * @var string
     */
    public $note;

    /**
     * @var int
     */
    public $type;

    private $_off;

    /**
     * OffForm constructor.
     * @param Off $off
     * @param array $config
     */
    public function __construct(Off $off, array $config = [])
    {
        $this->_off = $off;

        $this->startDate = $off->start_at;
        $this->endDate = $off->end_at;

        $this->note = !empty($off->note) ? $off->note : null;
        $this->type = $off->type;

        parent::__construct($config);
    }

    /**
     * @return Off
     */
    public function getOff(): Off
    {
        return $this->_off;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['startDate', 'endDate', 'type'], 'required'],
            [['type'], 'in', 'range' => [Off::TYPE_SHORT, Off::TYPE_VACATION]],
            [['endDate', 'startDate'], 'date', 'format' => 'yyyy-MM-dd'],
            [['startDate'], 'verifyStart'],
            [['endDate'], 'verifyEnd'],
            [['note'], 'string'],
        ];
    }

    /**
     * @throws Exception
     */
    public function verifyStart(): void
    {
        $conditions = [
            'and',
            ['user_id' => Yii::$app->user->id],
            ['<=', 'start_at', $this->startDate],
            ['>=', 'end_at', $this->startDate],
        ];

        if ($this->_off->id !== null) {
            $conditions[] = ['<>', 'id', $this->_off->id];
        }

        if (Off::find()->where($conditions)->exists()) {
            $this->addError('startDate', Yii::t('app', 'Selected day overlaps another off-time.'));
        }
    }

    /**
     * @throws Exception
     */
    public function verifyEnd(): void
    {
        if (Yii::$app->formatter->asTimestamp($this->startDate) > Yii::$app->formatter->asTimestamp($this->endDate)) {
            $this->addError('endDate', Yii::t('app', 'Off-time ending day can not be earlier than starting day.'));
        } else {
            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
                ['<=', 'start_at', $this->endDate],
                ['>=', 'end_at', $this->startDate],
            ];

            if ($this->_off->id !== null) {
                $conditions[] = ['<>', 'id', $this->_off->id];
            }

            if (Off::find()->where($conditions)->exists()) {
                $this->addError('endDate', Yii::t('app', 'Selected day overlaps another off-time.'));
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'startDate' => Yii::t('app', 'Start Day'),
            'endDate' => Yii::t('app', 'End Day'),
            'note' => Yii::t('app', 'Note'),
            'type' => Yii::t('app', 'Vacation'),
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->_off->user_id === null) {
            $this->_off->user_id = Yii::$app->user->id;
        }

        $originalType = (int)$this->_off->type;
        $originalStart = $this->_off->start_at;
        $originalEnd = $this->_off->end_at;

        $this->_off->start_at = $this->startDate;
        $this->_off->end_at = $this->endDate;
        $this->_off->type = (int)$this->type;
        $this->_off->note = $this->note !== '' ? $this->note : null;

        $sendInfo = false;

        if ((int)$this->type === Off::TYPE_VACATION
            && (
                $originalType !== Off::TYPE_VACATION
                || ($originalStart !== $this->startDate || $originalEnd !== $this->endDate)
            )) {
            $this->_off->approved = 0;
            $sendInfo = true;
        }

        if (!$this->_off->save()) {
            return false;
        }

        if ($sendInfo) {
            Off::sendInfoToAdmin($this->_off);
        }

        return true;
    }
}
