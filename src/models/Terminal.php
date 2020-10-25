<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

use function preg_match;
use function sha1;
use function time;

/**
 * Terminal model
 *
 * @property int $id
 * @property string $api_key
 */
class Terminal extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%terminal}}';
    }

    /**
     * @param $id
     * @return static|null
     */
    public static function findIdentity($id): ?self
    {
        return static::findOne(['id' => $id]);
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
     * Check if at least one terminal account is registered
     * @return bool
     */
    public static function isActive()
    {
        if (empty(static::find()->one())) {
            return false;
        }
        return true;
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return self
     * @throws ForbiddenHttpException
     */
    public static function findIdentityByAccessToken($token): self
    {
        if (!preg_match('/^(\d+):(\d+):(.+)$/', $token, $matches)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        [, $id, $stamp, $checksum] = $matches;

        $now = time();

        if ($now > $stamp + 60 || $now < $stamp - 60) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        $terminal = static::findIdentity($id);

        if ($terminal === null || empty($terminal->api_key) || !$terminal->verifyChecksum($stamp, $checksum)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        return $terminal;
    }

}
