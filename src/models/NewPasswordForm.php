<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;

use function count_chars;
use function log;
use function mb_strlen;

/**
 * Class NewPasswordForm
 * @package app\models
 */
class NewPasswordForm extends RegisterForm
{
    private $user;

    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['password'], 'string', 'min' => self::MIN_PASSWORD, 'max' => self::MAX_PASSWORD],
            [['password'], 'compare', 'compareAttribute' => 'emailAccount', 'operator' => '!='],
            [
                ['password'],
                function ($attribute) {
                    $entropy = 0;
                    $size = mb_strlen($this->$attribute, Yii::$app->charset ?: 'UTF-8');
                    foreach (count_chars($this->$attribute, 1) as $frequency) {
                        $p = $frequency / $size;
                        $entropy -= $p * log($p) / log(2);
                    }
                    if ($entropy < self::MIN_ENTROPY) {
                        $this->addError($attribute, Yii::t('app', 'You must provide more complex password.'));
                    }
                },
            ],
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        return Model::beforeValidate();
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('app', 'New Password'),
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

        $this->user->setPassword($this->password);
        $this->user->generateAuthKey();

        $this->user->removePasswordResetToken();

        if (!$this->user->save()) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));

            return false;
        }

        return true;
    }
}
