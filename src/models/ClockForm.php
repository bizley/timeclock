<?php

declare(strict_types=1);

namespace app\models;

use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

use function in_array;
use function is_array;

/**
 * Class ClockForm
 * @package app\models
 */
class ClockForm extends Model
{
    /**
     * @var string
     */
    public $note;

    /**
     * @var int
     */
    public $projectId;

    /**
     * @var string
     */
    public $startDate;

    /**
     * @var string
     */
    public $endTime;

    private $_clock;
    private $_originalStartDate;

    /**
     * ClockForm constructor.
     * @param Clock $session
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(Clock $session, array $config = [])
    {
        $this->_clock = $session;

        $this->note = !empty($session->note) ? $session->note : null;
        $this->projectId = !empty($session->project_id) ? $session->project_id : null;

        if ($session->clock_in) {
            $this->startDate = Yii::$app->formatter->asDatetime($session->clock_in, 'yyyy-MM-dd HH:mm');
            $this->_originalStartDate = Yii::$app->formatter->asDatetime($session->clock_in, 'yyyy-MM-dd HH:mm');
        }

        $this->endTime = $session->clock_out
            ? Yii::$app->formatter->asDatetime($session->clock_out, 'HH:mm')
            : null;

        parent::__construct($config);
    }

    /**
     * @return Clock
     */
    public function getSession(): Clock
    {
        return $this->_clock;
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
        ];
    }

    public function verifyTime(): void
    {
        if ($this->endTime !== null && (!preg_match(
                    '/^([0-2]\d):[0-5]\d$/',
                    $this->endTime,
                    $matches
                ) || $matches[1] > 23)) {
            $this->addError('endTime', Yii::t('app', 'Please provide proper time in HH:MM format.'));
        }
    }

    public function verifyProject(): void
    {
        if (!empty($this->projectId)) {
            $project = Project::findOne((int)$this->projectId);

            if ($project === null) {
                $this->addError('projectId', Yii::t('app', 'Can not find project of given ID.'));
            } elseif (!is_array($project->assignees) || !in_array(Yii::$app->user->id, $project->assignees, false)) {
                $this->addError('projectId', Yii::t('app', 'You are not assigned to selected project.'));
            }
        }
    }

    /**
     * @param string $date
     * @return DateTime
     * @throws Exception
     */
    public function prepareDateTime($date): DateTime
    {
        return new DateTime($date, new DateTimeZone(Yii::$app->timeZone));
    }

    /**
     * @return int
     * @throws Exception
     */
    public function prepareStart(): int
    {
        if ($this->startDate === $this->_originalStartDate) {
            return $this->_clock->clock_in;
        }

        return $this->prepareDateTime($this->startDate)->getTimestamp();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function prepareEnd(): int
    {
        $start = $this->prepareDateTime($this->startDate);

        return $this->prepareDateTime($start->format('Y-m-d') . ' ' . $this->endTime)->getTimestamp();
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
                ['user_id' => Yii::$app->user->id],
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
                    ['user_id' => Yii::$app->user->id],
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

        if ($this->_clock->user_id === null) {
            $this->_clock->user_id = Yii::$app->user->id;
        }

        $this->_clock->clock_in = $this->prepareStart();

        if (!empty($this->endTime)) {
            $this->_clock->clock_out = $this->prepareEnd();
        }

        $this->_clock->note = !empty($this->note) ? $this->note : null;
        $this->_clock->project_id = !empty($this->projectId) ? $this->projectId : null;

        return $this->_clock->save();
    }
}
