<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class RegisterForm
 * @package app\models
 */
class RegisterForm extends Model
{
    public const MIN_PASSWORD = 6;
    public const MAX_PASSWORD = 64;
    public const MIN_ENTROPY = 2;

    /**
     * @var string
     */
    public $emailAccount;

    /**
     * @var string
     */
    public $emailDomain;

    /**
     * @var
     */
    public $password;

    /**
     * @var
     */
    public $name;

    /**
     * @var string Email combined
     */
    public $email;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['emailAccount', 'emailDomain', 'email', 'password', 'name'], 'required'],
            [['emailAccount'], 'string'],
            [['emailDomain'], 'in', 'range' => ['@semfleet.tech', '@sagacapital.eu', '@semfleet.com']],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => User::class],
            [['password'], 'string', 'min' => self::MIN_PASSWORD, 'max' => self::MAX_PASSWORD],
            [['password'], 'compare', 'compareAttribute' => 'emailAccount', 'operator' => '!='],
            [['password'], function ($attribute) {
                $entropy = 0;
                $size = mb_strlen($this->$attribute, Yii::$app->charset ?: 'UTF-8');
                foreach (count_chars($this->$attribute, 1) as $frequency) {
                    $p = $frequency / $size;
                    $entropy -= $p * log($p) / log(2);
                }
                if ($entropy < self::MIN_ENTROPY) {
                    $this->addError($attribute, 'Musisz wybrać bardziej skomplikowane hasło.');
                }
            }],
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->email = $this->emailAccount . $this->emailDomain;

        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'emailAccount' => 'Email',
            'password' => 'Hasło',
            'name' => 'Imię i nazwisko',
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function register(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_EMPLOYEE;
        $user->email = $this->email;
        $user->name = $this->name;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if (!$user->save()) {
            Yii::$app->alert->danger('Wystąpił błąd podczas zapisu użytkownika.');
            return false;
        }

        return true;
    }
}
