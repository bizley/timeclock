<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Project model
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property array $assignees
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Project extends ActiveRecord
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_LOCKED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%project}}';
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
            [['name', 'color'], 'required'],
            [['name', 'color'], 'string'],
            [['name'], 'unique'],
            [['color'], 'match', 'pattern' => '/^#[0-9a-f]{0,6}$/'],
            [['assignees'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['assignees'], 'each', 'rule' => ['exist', 'targetClass' => User::class, 'targetAttribute' => 'id']],
        ];
    }

    /**
     * @param string|int $id
     */
    public static function lock($id): void
    {
        $project = static::findOne((int)$id);

        if ($project !== null && $project->status === self::STATUS_ACTIVE) {
            $project->status = self::STATUS_LOCKED;

            if (!$project->save(false, ['status', 'updated_at'])) {
                Yii::error(['Project locking error', $id]);
            }
        }
    }
}
