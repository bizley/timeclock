<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function date;
use function in_array;
use function is_array;

/**
 * Clock model
 *
 * @property int $id
 * @property int $user_id
 * @property int $clock_in
 * @property int $clock_out
 * @property string $note
 * @property int $created_at
 * @property int $updated_at
 * @property int $project_id
 *
 * @property Project $project
 * @property User $user
 */
class Clock extends ActiveRecord implements NoteInterface
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
     * @return array
     */
    public static function days(): array
    {
        return [
            1 => Yii::t('app', 'Mon'),
            2 => Yii::t('app', 'Tue'),
            3 => Yii::t('app', 'Wed'),
            4 => Yii::t('app', 'Thu'),
            5 => Yii::t('app', 'Fri'),
            6 => Yii::t('app', 'Sat'),
            7 => Yii::t('app', 'Sun'),
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
            [['clock_in'], 'integer'],
            [['note'], 'string'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['clock_out'], 'compare', 'compareAttribute' => 'clock_in', 'operator' => '>'],
            [['project_id'], 'verifyProject'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function verifyProject(): void
    {
        if (!empty($this->project_id)) {
            $project = Project::findOne((int)$this->project_id);

            if ($project === null) {
                $this->addError('project_id', Yii::t('app', 'Can not find project of given ID.'));
            } elseif (!is_array($project->assignees) || !in_array(Yii::$app->user->id, $project->assignees, false)) {
                $this->addError('project_id', Yii::t('app', 'You are not assigned to selected project.'));
            }
        }
    }

    /**
     * @return bool
     */
    public function isAnotherSessionSaved(): bool
    {
        $conditions = [
            'and',
            ['user_id' => Yii::$app->user->id],
            [
                'or',
                [
                    'and',
                    ['<', 'clock_in', $this->clock_out],
                    ['>=', 'clock_out', $this->clock_out],
                ],
                [
                    'and',
                    ['>=', 'clock_in', $this->clock_in],
                    ['<=', 'clock_out', $this->clock_out],
                ],
            ],
        ];

        return static::find()->where($conditions)->exists();
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        $now = static::roundToFullMinute((int)Yii::$app->formatter->asTimestamp('now'));

        if (static::find()->where(
            [
                'and',
                ['<', 'clock_in', $now],
                ['>', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
                [
                    'clock_out' => null,
                    'user_id' => Yii::$app->user->id,
                ],
            ]
        )->exists()) {
            Yii::$app->alert->danger(Yii::t('app', 'Session has already been started.'));

            return false;
        }

        $this->clock_in = $now;
        $this->clock_out = null;
        $this->user_id = Yii::$app->user->id;

        $note = Yii::$app->request->post('note');
        if (!empty($note)) {
            $this->note = $note;
        }

        $projectId = Yii::$app->request->post('project_id');
        if (!empty($projectId)) {
            $this->project_id = $projectId;
        }

        if (!$this->validate()) {
            Yii::error($this->errors);

            return false;
        }

        return $this->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->project_id !== null) {
            Project::lock($this->project_id);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        $this->clock_out = static::roundToFullMinute((int)Yii::$app->formatter->asTimestamp('now'));

        if (!$this->validate()) {
            Yii::error($this->errors);

            return false;
        }

        if ($this->isAnotherSessionSaved()) {
            Yii::$app->alert->danger(
                Yii::t('app', 'Can not end current session because it overlaps with another ended session.')
            );

            return false;
        }

        return $this->save(false);
    }

    /**
     * @return Clock|null
     */
    public static function session(): ?self
    {
        return static::find()->where(
            [
                'and',
                ['<', 'clock_in', (int)Yii::$app->formatter->asTimestamp('now')],
                ['>', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
                [
                    'clock_out' => null,
                    'user_id' => Yii::$app->user->id,
                ],
            ]
        )->orderBy(['clock_in' => SORT_DESC])->one();
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return !empty($this->note) ? $this->note : null;
    }

    /**
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * @param int $seconds
     * @return int
     */
    public static function roundToFullMinute(int $seconds): int
    {
        return $seconds - $seconds % 60;
    }
}
