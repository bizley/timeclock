<?php

declare(strict_types=1);

namespace app\controllers;

use app\assets\AppAsset;
use app\base\BaseController;
use app\models\ProfileForm;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

use function random_int;

/**
 * Class ProfileController
 * @package app\controllers
 */
class ProfileController extends BaseController
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
     * {@inheritdoc}
     */
    public function remember(): array
    {
        return array_merge(
            parent::remember(),
            [
                'index',
                'api',
                'pin',
            ]
        );
    }

    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionIndex()
    {
        $model = new ProfileForm();

        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->alert->success(Yii::t('app', 'Profile has been updated.'));

            return $this->refresh();
        }

        return $this->render(
            'index',
            [
                'model' => $model,
                'projects' => ['' => ''] + Yii::$app->user->identity->assignedProjects,
            ]
        );
    }

    /**
     * @return Response
     * @throws Exception
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
     * @throws Exception
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
     * @param string $theme
     * @return Response
     */
    public function actionTheme(string $theme): Response
    {
        $user = User::findOne(['id' => Yii::$app->user->id]);

        if ($user === null) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find user of given ID.'));
        } elseif (!in_array($theme, AppAsset::themes(), true)) {
            Yii::$app->alert->danger(Yii::t('app', 'Can not find theme of given name.'));
        } else {
            $user->theme = $theme;

            if (!$user->save()) {
                Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));
            }
        }

        return $this->redirect(Url::previous('rememberedUrl'));
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
            $pinHash = Yii::$app->security->generatePasswordHash($pin);
        } while (User::find()->where(['pin_hash' => $pinHash])->exists());

        $user->pin_hash = $pinHash;

        if (!$user->save(true, ['pin_hash', 'updated_at'])) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));

            return $this->redirect(['index']);
        }

        return $this->render(
            'pin',
            [
                'pin' => $pin,
            ]
        );
    }
}
