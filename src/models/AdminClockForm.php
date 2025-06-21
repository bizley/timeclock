<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class ClockForm
 * @package app\models
 */
class AdminClockForm extends ClockForm
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var Clock
     */
    private $_clock;

    /**
     * ClockForm constructor.
     * @param Clock $session
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(Clock $session, array $config = [])
    {
        $this->_clock = $session;
        $this->userId = !empty($session->user_id) ? $session->user_id : null;
        parent::__construct($session, $config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['startDate'], 'required'],
            [['startDate'], 'verifyStart'],
            [['endTime'], 'verifyTime'],
            [['endTime'], 'verifyEnd'],
            [['note'], 'string'],
            [['projectId'], 'verifyProject'],
            [['userId'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
        ];
    }

    public function verifyProject(): void
    {
        if (!empty($this->projectId)) {
            $project = Project::findOne((int)$this->projectId);

            if ($project === null) {
                $this->addError('projectId', Yii::t('app', 'Can not find project of given ID.'));
            } elseif (!is_array($project->assignees) || !in_array($this->userId, $project->assignees, false)) {
                $this->addError('projectId', Yii::t('app', 'You are not assigned to selected project.'));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function verifyStart(): void
    {
        if (!$this->hasErrors()) {
            $start = $this->prepareStart();

            $conditions = [
                'and',
                ['user_id' => $this->userId],
                ['<=', 'clock_in', $start],
                ['>', 'clock_out', $start],
            ];

            if ($this->_clock->id !== null) {
                $conditions[] = ['<>', 'id', $this->_clock->id];
            }

            if (Clock::find()->where($conditions)->exists()) {
                $this->addError('startDate', Yii::t('app', 'Selected hour overlaps another ended session.'));
            }

            $this->_clock->clock_in = $start;
        }
    }

    /**
     * @throws Exception
     */
    public function verifyEnd(): void
    {
        if (!$this->hasErrors()) {
            $start = $this->prepareStart();
            $end = $this->prepareEnd();

            if ($start >= $end) {
                $this->addError('endTime', Yii::t('app', 'Session ending hour must be later than starting hour.'));
            } else {
                $conditions = [
                    'and',
                    ['user_id' => $this->userId],
                    ['<', 'clock_in', $end],
                    ['>', 'clock_out', $start],
                ];

                if ($this->_clock->id !== null) {
                    $conditions[] = ['<>', 'id', $this->_clock->id];
                }

                if (Clock::find()->where($conditions)->exists()) {
                    $this->addError('endTime', Yii::t('app', 'Selected session time overlaps another ended session.'));
                }
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'note' => Yii::t('app', 'Note'),
            'projectId' => Yii::t('app', 'Project'),
            'startDate' => Yii::t('app', 'Start'),
            'endTime' => Yii::t('app', 'End'),
            'userId' => Yii::t('app', 'Name'),
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

        $this->_clock->clock_in = $this->prepareStart();

        if (!empty($this->endTime)) {
            $this->_clock->clock_out = $this->prepareEnd();
        }

        $this->_clock->note = !empty($this->note) ? $this->note : null;
        $this->_clock->project_id = !empty($this->projectId) ? $this->projectId : null;
        $this->_clock->user_id = !empty($this->userId) ? $this->userId : null;

        return $this->_clock->save();
    }
}
