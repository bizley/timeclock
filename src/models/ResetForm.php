<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Exception;
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
            'email' => Yii::t('app', 'Email'),
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function reset(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findByEmail($this->email);

        if ($user !== null && $user->status !== User::STATUS_DELETED) {
            $user->generatePasswordResetToken();

            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));

                return false;
            }

            $mail = Yii::$app->mailer->compose(
                [
                    'html' => 'reset-html',
                    'text' => 'reset-text',
                ],
                [
                    'user' => $user->name,
                    'link' => Url::to(['site/new-password', 'token' => $user->password_reset_token], true),
                ]
            )
                ->setFrom(Yii::$app->params['email'])
                ->setTo([$user->email => $user->name])
                ->setSubject(
                    Yii::t(
                        'app',
                        'Password reset at {company} Timeclock system',
                        ['company' => Yii::$app->params['company']]
                    )
                );

            if (!$mail->send()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while sending password reset link email.'));

                return false;
            }
        }

        return true;
    }
}
