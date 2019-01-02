<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class ProfileForm
 * @package app\models
 */
class ProfileForm extends RegisterForm
{
    public function init(): void
    {
        parent::init();

        $this->name = Yii::$app->user->identity->name;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
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
                    $this->addError($attribute, Yii::t('app', 'You must provide more complex password.'));
                }
            }],
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
            'name' => Yii::t('app', 'First And Last Name'),
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function update(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;
        $user->name = $this->name;

        if (!empty($this->password)) {
            $user->setPassword($this->password);
            $user->generateAuthKey();
        }

        if (!$user->save()) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            return false;
        }

        return true;
    }
}
