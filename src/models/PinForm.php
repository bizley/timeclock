<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

use function substr;

/**
 * Class PinForm
 * @package app\models
 */
class PinForm extends Model
{
    /**
     * @var string
     */
    public $pin;

    /**
     * @var bool
     */
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['pin'], 'required'],
            [['pin'], 'number'],
            [['rememberMe'], 'boolean'],
            [['pin'], 'validatePin'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pin' => Yii::t('app', 'PIN'),
            'rememberMe' => Yii::t('app', 'Remember me at this device'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     * @param string $attribute the attribute currently being validated
     */
    public function validatePin($attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePin($this->pin)) {
                $this->addError($attribute, Yii::t('app', 'Invalid PIN.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    /**
     * Finds user by email
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === false) {
            $id = substr($this->pin, 0, -3); // last 3 digits is actual PIN, rest is user ID
            $this->_user = User::findOne(['id' => $id]);
        }

        return $this->_user;
    }
}
