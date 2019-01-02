<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ProfileForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'grant' => ['post'],
                    'revoke' => ['post'],
                    'change' => ['post'],
                    'pin' => ['post'],
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
     * @throws \yii\base\Exception
     */
    public function actionGrant(): Response
    {
        do {
            $apiKey = Yii::$app->security->generateRandomString(20);
        } while (User::find()->where(['api_key' => $apiKey])->exists());

        Yii::$app->user->identity->api_key = $apiKey;

        if (!Yii::$app->user->identity->save()) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
        } else {
            Yii::$app->alert->success(Yii::t('app', 'API access has been granted.'));
        }

        return $this->redirect('index');
    }

    /**
     * @return Response
     */
    public function actionRevoke(): Response
    {
        Yii::$app->user->identity->api_key = null;

        if (!Yii::$app->user->identity->save()) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
        } else {
            Yii::$app->alert->success(Yii::t('app', 'API access has been revoked.'));
        }

        return $this->redirect('index');
    }

    /**
     * @return Response
     * @throws \yii\base\Exception
     */
    public function actionChange(): Response
    {
        do {
            $apiKey = Yii::$app->security->generateRandomString(20);
        } while (User::find()->where(['api_key' => $apiKey])->exists());

        Yii::$app->user->identity->api_key = $apiKey;

        if (!Yii::$app->user->identity->save()) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
        } else {
            Yii::$app->alert->success(Yii::t('app', 'API key has been changed.'));
        }

        return $this->redirect('index');
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

    /**
     * @return string
     */
    public function actionApi(): string
    {
        return $this->render('api');
    }

    /**
     * @return string|Response
     * @throws \Exception
     */
    public function actionPin()
    {
        $user = User::findOne(['id' => Yii::$app->user->id]);

        if ($user === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
            return $this->redirect(['index']);
        }

        do {
            $pin = $user->id . random_int(0, 9) . random_int(0, 9) . random_int(0, 9);
            $pinHash = Yii::$app->security->generatePasswordHash($pin, 15);
        } while (User::find()->where(['pin_hash' => $pinHash])->exists());

        $user->pin_hash = $pinHash;

        if (!$user->save(true, ['pin_hash', 'updated_at'])) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            return $this->redirect(['index']);
        }

        return $this->render('pin', [
            'pin' => $pin,
        ]);
    }
}
