<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Class ResetForm
 * @package app\models
 */
class ResetForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function reset(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findByEmail($this->email);

        if ($user !== null) {
            $user->generatePasswordResetToken();

            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
                return false;
            }

            $mail = Yii::$app->mailer->compose([
                    'html' => 'reset-html',
                    'text' => 'reset-text',
                ], [
                    'user' => $user->name,
                    'link' => Url::to(['site/new-password', 'token' => $user->password_reset_token], true)
                ])
                ->setFrom('notice@semfleet.com')
                ->setTo([$user->email => $user->name])
                ->setSubject('Reset hasła w Company Timeclock');

            if (!$mail->send()) {
                Yii::$app->alert->danger('Wystąpił błąd podczas wysyłąnia emaila z linkiem resetującym hasło.');
                return false;
            }
        }

        return true;
    }
}
