<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ProfileForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class ProfileController
 * @package app\controllers
 */
class ProfileController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string|Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $model = new ProfileForm();

        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->alert->success(Yii::t('app', 'Profile has been updated.'));
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     */
    public function actionDark(): Response
    {
        $user = User::findOne(['id' => Yii::$app->user->id]);

        if ($user === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } else {
            $user->theme = 'dark';

            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            }
        }

        return $this->goBack();
    }

    /**
     * @return Response
     */
    public function actionLight(): Response
    {
        $user = User::findOne(['id' => Yii::$app->user->id]);

        if ($user === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } else {
            $user->theme = 'light';

            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            }
        }

        return $this->goBack();
    }
}
