<?php

declare(strict_types=1);

namespace app\api\models;

use app\models\User;
use DateTime;
use DateTimeZone;
use Exception;
use Yii;

use function in_array;
use function is_array;

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
     * @var int
     */
    public $defaultProject;

    /**
     * @var int
     */
    public $projectId;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['defaultProject'], 'default', 'value' => 1, 'except' => 'update'],
            [['defaultProject'], 'default', 'value' => 0, 'on' => 'update'],
            [['clockIn', 'user_id'], 'required'], // clockIn can be null before validate
            [['clockIn', 'clockOut', 'defaultProject', 'projectId'], 'integer'],
            [['projectId'], 'integer', 'min' => 0],
            [['defaultProject'], 'in', 'range' => [0, 1]],
            [['clockOut'], 'compare', 'compareAttribute' => 'clockIn', 'operator' => '>'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['clockIn'], 'checkClockIn'],
            [['clockOut'], 'checkClockOut'],
            [['clockOut'], 'checkOverMidnight'],
            [['clockOut'], 'checkClockBetween'],
            [['note'], 'string'],
            [['projectId'], 'verifyProject'],
        ];
    }

    public function verifyProject(): void
    {
        if (!empty($this->projectId) && $this->projectId > 0) {
            $project = Project::findOne((int)$this->projectId);

            if ($project === null) {
                $this->addError('projectId', Yii::t('app', 'Can not find project of given ID.'));
            } elseif (!is_array($project->assignees) || !in_array(Yii::$app->user->id, $project->assignees, false)) {
                $this->addError('projectId', Yii::t('app', 'You are not assigned to selected project.'));
            }
        }
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
            'note',
            'project' => static function ($model) {
                /* @var $model Clock */
                return $model->project;
            },
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

        if (empty($this->note)) {
            $this->note = null;
        }

        if ((int)$this->defaultProject === 1 && $this->projectId === null) {
            $this->project_id = Yii::$app->user->identity->project_id;
        }

        if ($this->projectId !== null) {
            if ((int)$this->projectId === 0) {
                $this->project_id = null;
            } else {
                $this->project_id = (int)$this->projectId;
            }
        }

        parent::afterValidate();
    }

    public function checkClockIn(): void
    {
        if (!$this->hasErrors()) {
            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
                ['<', 'clock_in', $this->clockIn],
                ['>', 'clock_out', $this->clockIn],
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
                ['user_id' => Yii::$app->user->id],
                ['<', 'clock_in', $this->clockOut],
                ['>', 'clock_out', $this->clockOut],
            ];

            if ($this->scenario === 'update') {
                $conditions[] = ['<>', 'id', $this->id];
            }

            if (static::find()->where($conditions)->exists()) {
                $this->addError('clockOut', Yii::t('app', 'Can not end session because it overlaps with another ended session.'));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function checkOverMidnight(): void
    {
        if ($this->clockOut !== null && !$this->hasErrors()) {
            $clockInDay = (new DateTime('now', new DateTimeZone(Yii::$app->timeZone)))->setTimestamp($this->clockIn);
            $clockOutDay = (new DateTime('now', new DateTimeZone(Yii::$app->timeZone)))->setTimestamp($this->clockOut);

            if ($clockInDay->format('Ymd') !== $clockOutDay->format('Ymd')) {
                $this->addError('clockOut', Yii::t('app', 'Session can not last through midnight.'));
            }
        }
    }

    public function checkClockBetween(): void
    {
        if ($this->clockOut !== null && !$this->hasErrors()) {
            $conditions = [
                'and',
                ['user_id' => Yii::$app->user->id],
                ['>', 'clock_in', $this->clockIn],
                ['<', 'clock_out', $this->clockOut],
            ];

            if ($this->scenario === 'update') {
                $conditions[] = ['<>', 'id', $this->id];
            }

            if (static::find()->where($conditions)->exists()) {
                $this->addError('clockOut', Yii::t('app', 'Can not modify session because it overlaps with another ended session.'));
            }
        }
    }
}
