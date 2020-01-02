<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;

use function date;
use function mb_strtoupper;
use function preg_match;
use function preg_split;
use function sha1;
use function strrpos;
use function substr;
use function time;

/**
 * User model
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property string $password_hash
 * @property string $pin_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $theme
 * @property string $api_key
 * @property int $project_id
 * @property int $role
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 *
 * @property string $initials
 * @property array $assignedProjects
 * @property Project $defaultProject
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 9;
    public const STATUS_REGISTERED = 0;
    public const STATUS_ACTIVE = 1;

    public const ROLE_EMPLOYEE = 1;
    public const ROLE_ADMIN = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
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
            ['status', 'default', 'value' => self::STATUS_REGISTERED],
            ['status', 'in', 'range' => [self::STATUS_REGISTERED, self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'name' => Yii::t('app', 'First And Last Name'),
            'phone' => Yii::t('app', 'Phone Number'),
            'password' => Yii::t('app', 'Password'),
            'role' => Yii::t('app', 'Role'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): ?self
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return self
     * @throws ForbiddenHttpException
     */
    public static function findIdentityByAccessToken($token, $type = null): self
    {
        if (!preg_match('/^(\d+):(\d+):(.+)$/', $token, $matches)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        [, $userId, $stamp, $checksum] = $matches;

        $now = time();

        if ($now > $stamp + 60 || $now < $stamp - 60) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        $user = static::findIdentity($userId);

        if ($user === null || empty($user->api_key) || !$user->verifyChecksum($stamp, $checksum)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        return $user;
    }

    /**
     * @param string $stamp
     * @param string $checksum
     * @return bool
     */
    public function verifyChecksum(string $stamp, string $checksum): bool
    {
        return sha1($stamp . $this->api_key) === $checksum;
    }

    /**
     * Finds user by username
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds user by password reset token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token): ?self
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * Finds out if password reset token is valid
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = 3 * 24 * 60 * 60;

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Validates PIN
     * @param string $pin PIN to validate
     * @return bool if PIN provided is valid for current user
     */
    public function validatePin(string $pin): bool
    {
        return Yii::$app->security->validatePassword($pin, $this->pin_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     * @throws Exception
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    private $_initials;

    /**
     * @return string
     */
    public function getInitials(): string
    {
        if ($this->_initials === null) {
            $words = preg_split('/[\s\-_\.,]/', $this->name);
            $this->_initials = '';

            foreach ($words as $word) {
                $this->_initials .= mb_strtoupper(substr($word, 0, 1), 'UTF-8');
            }
        }

        return $this->_initials;
    }

    /**
     * @return bool
     */
    public function isClockActive(): bool
    {
        return Clock::find()->where(
            [
                'and',
                ['>=', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
                ['<', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 23:59:59'))],
                [
                    'clock_out' => null,
                    'user_id' => $this->id,
                ],
            ]
        )->exists();
    }

    /**
     * @return Clock|null
     */
    public function latestSession(): ?Clock
    {
        $clock = Clock::find()->where(
            [
                'and',
                ['>=', 'clock_in', Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
                [
                    'clock_out' => null,
                    'user_id' => $this->id,
                ],
            ]
        )->orderBy(['clock_in' => SORT_DESC])->one();

        return $clock;
    }

    /**
     * @return int|null
     */
    public function sessionStartedAt(): ?int
    {
        $latestSession = $this->latestSession();

        if ($latestSession === null) {
            return null;
        }

        return $latestSession->clock_in;
    }

    /**
     * @return array
     */
    public function todaysSessions(): array
    {
        return Clock::find()->where(
            [
                'and',
                ['>=', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
                ['<=', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 23:59:59'))],
                ['user_id' => $this->id],
            ]
        )->orderBy(['clock_in' => SORT_DESC])->all();
    }

    /**
     * @return Clock|null
     */
    public function getOldOpenedSession(): ?Clock
    {
        return Clock::find()->where(
            [
                'and',
                ['<', 'clock_in', (int)Yii::$app->formatter->asTimestamp(date('Y-m-d 00:00:00'))],
                [
                    'clock_out' => null,
                    'user_id' => $this->id,
                ],
            ]
        )->orderBy(['clock_in' => SORT_ASC])->one();
    }

    /**
     * @return array
     */
    public function getAssignedProjects(): array
    {
        return ArrayHelper::map(
            Project::find()
                ->where(
                    [
                        'and',
                        new Expression('JSON_CONTAINS(`assignees`, :user)'),
                        ['<>', 'status', Project::STATUS_DELETED],
                    ]
                )
                ->params([':user' => (string)Yii::$app->user->id])
                ->orderBy(['name' => SORT_ASC])
                ->all(),
            'id',
            'name'
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getDefaultProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }
}
